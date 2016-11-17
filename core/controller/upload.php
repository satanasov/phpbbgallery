<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

class upload
{
	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbbgallery\core\misc */
	protected $misc;

	/* @var \phpbbgallery\core\album\album */
	protected $album;

	/* @var \phpbbgallery\core\album\album */
	protected $display;

	/**
	 * Constructor
	 *
	 * @param \phpbb\request\request $request
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\user $user User object
	 * @param \phpbb\template\template $template
	 * @param \phpbb\config\config $config
	 * @param Container|ContainerInterface $phpbb_container
	 * @param \phpbbgallery\core\album\album $album Album class
	 * @param \phpbbgallery\core\misc $misc Misc class
	 * @param \phpbbgallery\core\auth\auth $auth
	 * @param \phpbbgallery\core\album\display $display Display class
	 * @param \phpbb\controller\helper $helper
	 * @param \phpbbgallery\core\config $gallery_config
	 * @param \phpbbgallery\core\user $gallery_user
	 * @param \phpbbgallery\core\image\image $image
	 * @param \phpbbgallery\core\notification $gallery_notification
	 * @param \phpbbgallery\core\notification\helper $notification_helper
	 * @param \phpbbgallery\core\url $url
	 * @param \phpbbgallery\core\upload $gallery_upload
	 * @param \phpbbgallery\core\block $block
	 * @param                                        $images_table
	 * @param $phpbb_root_path
	 */

	public function __construct(\phpbb\request\request $request, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\template\template $template,
	\phpbb\config\config $config, ContainerInterface $phpbb_container,
	\phpbbgallery\core\album\album $album, \phpbbgallery\core\misc $misc, \phpbbgallery\core\auth\auth $auth, \phpbbgallery\core\album\display $display,
	\phpbb\controller\helper $helper, \phpbbgallery\core\config $gallery_config, \phpbbgallery\core\user $gallery_user, \phpbbgallery\core\image\image $image,
	\phpbbgallery\core\notification $gallery_notification, \phpbbgallery\core\notification\helper $notification_helper, \phpbbgallery\core\url $url, \phpbbgallery\core\upload $gallery_upload,
	\phpbbgallery\core\block $block,
	$images_table, $phpbb_root_path)
	{
		$this->request = $request;
		$this->db = $db;
		$this->user = $user;
		$this->template = $template;
		$this->config = $config;
		$this->phpbb_container = $phpbb_container;
		$this->album = $album;
		$this->misc = $misc;
		$this->auth = $auth;
		$this->display = $display;
		$this->helper = $helper;
		$this->gallery_config = $gallery_config;
		$this->gallery_user = $gallery_user;
		$this->image = $image;
		$this->url = $url;
		$this->gallery_upload = $gallery_upload;
		$this->gallery_notification = $gallery_notification;
		$this->notification_helper = $notification_helper;
		$this->block = $block;
		$this->images_table = $images_table;
		$this->phpbb_root_path = $phpbb_root_path;
	}

	public function main($album_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$album_data = $this->album->get_info($album_id);
		$this->display->generate_navigation($album_data);
		add_form_key('gallery');
		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id'	=> $album_id));
		$album_loginlink = 'ucp.php?mode=login';
		$error = '';
		//Let's get authorization
		$this->auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->auth->acl_check('i_upload', $album_id, $album_data['album_user_id']) || ($album_data['album_status'] == $this->block->get_album_status_locked()))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}
		$page_title = 'Upload to "' . $album_data['album_name'] . '"';

		// Before all
		if (!$this->check_fs())
		{
			trigger_error('NO_WRITE_ACCESS');
		}
		$submit = $this->request->variable('submit', false);
		$mode = $this->request->variable('mode', 'upload');
		// So let's see if we have AJAX and use jQuery shit.
		// We are going to use ajax uplaod only for registered users.
		// Anons should suffer.
		if ($this->request->is_ajax() && $this->user->data['is_registered'])
		{
			// So we use ajax request to upload (so we are going to copy some functions from other upload
			// Upload Quota Check
			// 1. Check album-configuration Quota
			if (($this->gallery_config->get('album_images') >= 0) && ($album_data['album_images'] >= $this->gallery_config->get('album_images')))
			{
				//@todo: Add return link
				trigger_error('ALBUM_REACHED_QUOTA');
			}

			// 2. Check user-limit, if he is not allowed to go unlimited
			if (!$this->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id']))
			{
				$sql = 'SELECT COUNT(image_id) count
					FROM ' . $this->images_table . '
					WHERE image_user_id = ' . $this->user->data['user_id'] . '
						AND image_status <> ' . $this->block->get_image_status_orphan() . '
						AND image_album_id = ' . (int) $album_id;
				$result = $this->db->sql_query($sql);
				$own_images = (int) $this->db->sql_fetchfield('count');
				$this->db->sql_freeresult($result);
				if ($own_images >= $this->auth->acl_check('i_count', $album_id, $album_data['album_user_id']))
				{
					//@todo: Add return link
					trigger_error($this->user->lang('USER_REACHED_QUOTA', $this->auth->acl_check('i_count', $album_id, $album_data['album_user_id'])));
				}
			}
			$upload_files_limit = ($this->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id'])) ? $this->gallery_config->get('num_uploads') : min(($this->auth->acl_check('i_count', $album_id, $album_data['album_user_id']) - $own_images), $this->gallery_config->get('num_uploads'));
			$process = $this->gallery_upload;
			$process->set_up($album_id, $upload_files_limit);
			$process->set_username($this->user->data['username']);
			$process->set_allow_comments(1);
			$process->upload_file(1);
			if (!empty($process->errors))
			{
				return new \Symfony\Component\HttpFoundation\JsonResponse(array(
					'files'	=> array(
						array(
							'error'	=> implode(',', $process->errors)
						)
					)
				));
			}
			$checks = $process->generate_hidden_fields();
			$process->get_images($checks);
			$image_names = array();
			foreach ($process->images as $ID)
			{
				$image_names[] = $process->image_data[$ID]['image_name'];
			}
			$process->set_names($image_names);

			$success = true;
			foreach ($process->images as $image_id)
			{
				$success = $success && $process->update_image($image_id, !$this->auth->acl_check('i_approve', $album_id, $album_data['album_user_id']), $album_data['album_contest']);
				if ($this->gallery_user->get_data('watch_own'))
				{
					$this->gallery_notification->add($image_id);
				}
			}

			if ($this->auth->acl_check('i_approve', $album_id, $album_data['album_user_id']))
			{
				//$this->notification_helper->notify_album($album_id, $this->user->data['user_id']);
				$data = array(
					'targets'	=> array($this->user->data['user_id']),
					'album_id'	=> $album_id,
					'last_image'	=> end($process->images),
				);
				$this->notification_helper->new_image($data);
			}
			else
			{
				$target = array(
					'album_id'	=>	$album_id,
					'last_image'	=> end($process->images),
					'uploader'		=> $this->user->data['user_id'],
				);
				$this->notification_helper->notify('approval', $target);
			}
			// ToDo - notifications!!!
			//$phpbb_gallery_notification->send_notification('album', $album_id, $image_names[0]);
			$this->image->handle_counter($process->images, true);
			$this->album->update_info($album_id);

			// So if all is fine let's prepare response
			$response = array();
			foreach ($process->images as $ID)
			{
				$response[] = array(
					'url' => $this->helper->route('phpbbgallery_core_image', array('image_id' => $ID)),
					'thumbnail'	=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $ID)),
					'name'	=> $process->image_data[$ID]['image_name'],
					//	'type'	=> $process->image_data[$process->images[0]]['image_name'],
					'size'	=> $process->image_data[$ID]['filesize_upload'],
					//	'delete_url'	=> '',
					//	'delete_type'	=> ''
				);
			}
			return new \Symfony\Component\HttpFoundation\JsonResponse(array(
				'files'	=> $response
			));

		}
		if ($mode == 'upload')
		{
			// Upload Quota Check
			// 1. Check album-configuration Quota
			if (($this->gallery_config->get('album_images') >= 0) && ($album_data['album_images'] >= $this->gallery_config->get('album_images')))
			{
				//@todo: Add return link
				trigger_error('ALBUM_REACHED_QUOTA');
			}

			// 2. Check user-limit, if he is not allowed to go unlimited
			if (!$this->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id']))
			{
				$sql = 'SELECT COUNT(image_id) count
					FROM ' . $this->images_table . '
					WHERE image_user_id = ' . $this->user->data['user_id'] . '
						AND image_status <> ' . $this->block->get_image_status_orphan() . '
						AND image_album_id = ' . (int) $album_id;
				$result = $this->db->sql_query($sql);
				$own_images = (int) $this->db->sql_fetchfield('count');
				$this->db->sql_freeresult($result);
				if ($own_images >= $this->auth->acl_check('i_count', $album_id, $album_data['album_user_id']))
				{
					//@todo: Add return link
					trigger_error($this->user->lang('USER_REACHED_QUOTA', $this->auth->acl_check('i_count', $album_id, $album_data['album_user_id'])));
				}
			}

			if ($this->misc->display_captcha('upload'))
			{
				$captcha = $this->phpbb_container->get('captcha.factory')->get_instance($this->config['captcha_plugin']);
				$captcha->init(CONFIRM_POST);
				$s_captcha_hidden_fields = '';
				$this->template->assign_vars(array(
					'S_CONFIRM_CODE'		=> true,
					'CAPTCHA_TEMPLATE'		=> $captcha->get_template(),
				));

			}

			$upload_files_limit = ($this->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id'])) ? $this->gallery_config->get('num_uploads') : min(($this->auth->acl_check('i_count', $album_id, $album_data['album_user_id']) - $own_images), $this->gallery_config->get('num_uploads'));
			$process = $this->gallery_upload;
			$process->set_up($album_id, $upload_files_limit);
			if ($submit)
			{
				if (!check_form_key('gallery'))
				{
					trigger_error('FORM_INVALID');
				}
				//$process->set_rotating($this->request->variable('rotate', array(0)));
				$process->set_allow_comments($this->request->variable('allow_comments', false));

				if ($this->misc->display_captcha('upload'))
				{
					$captcha_error = $captcha->validate();
					if ($captcha_error !== false)
					{
						$process->new_error($captcha_error);
					}
				}

				if (!$this->user->data['is_registered'])
				{
					$username = $this->request->variable('username', $this->user->data['username']);
					if (!function_exists('validate_username'))
					{
						$this->url->_include(array('functions_user'), 'phpbb');
					}
					if ($result = validate_username($username))
					{
						$this->user->add_lang('ucp');
						$error_array[] = $this->user->lang[$result . '_USERNAME'];
					}
					else
					{
						$process->set_username($username);
					}
				}

				if (empty($process->errors))
				{
					$files = $this->request->variable('files', array('name'=> array('' => ''), 'type' => array('' => ''), 'tmp_name' => array('' => ''), 'error' =>  array('' => ''), 'size' => array('' => '')), true, \phpbb\request\request_interface::FILES);
					$count = count($files['name']);
					if ($count <= $upload_files_limit)
					{
						$process->upload_file($count);
					}
				}

				if (!$process->uploaded_files)
				{
					$process->new_error($this->user->lang['UPLOAD_NO_FILE']);
				}
				else
				{
					$mode = 'upload_edit';
					// Remove submit, so we get the first screen of step 2.
					$submit = false;
				}

				$error = implode('<br />', $process->errors);

				/*if (phpbb_gallery_misc::display_captcha('upload'))
				{
					$captcha->reset();
				}*/
			}

			if ($mode == 'upload')
			{
				$this->template->assign_vars(array(
					'ERROR'					=> $error,
					'S_MAX_FILESIZE'		=> get_formatted_filesize($this->gallery_config->get('max_filesize')),
					'S_MAX_WIDTH'			=> $this->gallery_config->get('max_width'),
					'S_MAX_HEIGHT'			=> $this->gallery_config->get('max_height'),
					'S_ALLOWED_FILETYPES'	=> implode(', ', $process->get_allowed_types(true)),
					'S_ALBUM_ACTION'		=>  $this->helper->route('phpbbgallery_core_album_upload', array('album_id' => $album_id)),
					'S_UPLOAD'				=> true,
					'S_ALLOW_ROTATE'		=> ($this->gallery_config->get('allow_rotate') && function_exists('imagerotate')),
					'S_UPLOAD_LIMIT'		=> $upload_files_limit,
					'S_COMMENTS_ENABLED'	=> $this->gallery_config->get('allow_comments') && $this->gallery_config->get('comment_user_control'),
					'S_ALLOW_COMMENTS'		=> true,
					'L_ALLOW_COMMENTS'		=> $this->user->lang('ALLOW_COMMENTS_ARY', $upload_files_limit),
				));

				// The quick upload will work only for registered users!
				// So fuck you bots and anons
				if ($this->user->data['is_registered'])
				{
					$filetypes = array();
					foreach ($process->get_allowed_types(true) as $VAR)
					{
						if ($VAR == 'jpg')
						{
							$filetypes[] = 'jpe?g';
						}
						if ($VAR == 'zip')
						{
							continue;
						}
						else
						{
							$filetypes[] = $VAR;
						}
					}
					$this->template->assign_vars(array(
						'S_GALLERY_QUICK_UPLOAD'	=> true,
						'S_QUICK_MAX_FILESIZE'	=> $this->gallery_config->get('max_filesize'),
						'S_QUICK_FILE_TYPES' => '/(\.|\/)(' . implode('|', $filetypes) . ')$/i',
					));
				}
				/*if (phpbb_gallery_misc::display_captcha('upload'))
				{
					if (!$submit || !$captcha->is_solved())
					{
						$template->assign_vars(array(
							'S_CONFIRM_CODE'			=> true,
							'CAPTCHA_TEMPLATE'			=> $captcha->get_template(),
						));
					}
					$template->assign_vars(array(
						'S_CAPTCHA_HIDDEN_FIELDS'	=> $s_captcha_hidden_fields,
					));
				}*/
			}
		}
		if ($mode == 'upload_edit')
		{
			if ($submit)
			{
				// Upload Quota Check
				// 1. Check album-configuration Quota
				if (($this->gallery_config->get('album_images') >= 0) && ($album_data['album_images'] >= $this->gallery_config->get('album_images')))
				{
					//@todo: Add return link
					trigger_error('ALBUM_REACHED_QUOTA');
				}

				// 2. Check user-limit, if he is not allowed to go unlimited
				if (!$this->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id']))
				{
					$sql = 'SELECT COUNT(image_id) count
						FROM ' . $this->images_table . '
						WHERE image_user_id = ' . $this->user->data['user_id'] . '
							AND image_status <> ' . $this->block->get_image_status_orphan() . '
							AND image_album_id = ' . (int) $album_id;
					$result = $this->db->sql_query($sql);
					$own_images = (int) $this->db->sql_fetchfield('count');
					$this->db->sql_freeresult($result);
					if ($own_images >= $this->auth->acl_check('i_count', $album_id, $album_data['album_user_id']))
					{
						//@todo: Add return link
						trigger_error($this->user->lang('USER_REACHED_QUOTA', $this->auth->acl_check('i_count', $album_id, $album_data['album_user_id'])));
					}
				}
				$description_array = $this->request->variable('message', array(''), true);
				foreach ($description_array as $var)
				{
					if (strlen($var) > $this->gallery_config->get('description_length'))
					{
						trigger_error($this->user->lang('DESC_TOO_LONG'));
					}
				}
				$upload_files_limit = ($this->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id'])) ? $this->gallery_config->get('num_uploads') : min(($this->auth->acl_check('i_count', $album_id, $album_data['album_user_id']) - $own_images), $this->gallery_config->get('num_uploads'));

				$upload_ids = $this->request->variable('upload_ids', array(''));

				$process = $this->gallery_upload;
				$process->set_up($album_id, $upload_files_limit);
				$process->set_rotating($this->request->variable('rotate', array(0)));
				$process->get_images($upload_ids);
				$image_names = $this->request->variable('image_name', array(''), true);
				$process->set_names($image_names);
				$process->set_descriptions($description_array);
				$process->set_image_num($this->request->variable('image_num', 0));
				$process->use_same_name($this->request->variable('same_name', false));

				$success = true;
				foreach ($process->images as $image_id)
				{
					$success = $success && $process->update_image($image_id, !$this->auth->acl_check('i_approve', $album_id, $album_data['album_user_id']), $album_data['album_contest']);
					if ($this->gallery_user->get_data('watch_own'))
					{
						$this->gallery_notification->add($image_id);
					}
				}

				$message = '';
				$error = implode('<br />', $process->errors);
				if ($this->auth->acl_check('i_approve', $album_id, $album_data['album_user_id']))
				{
					$message .= (!$error) ? $this->user->lang['ALBUM_UPLOAD_SUCCESSFUL'] : $this->user->lang('ALBUM_UPLOAD_SUCCESSFUL_ERROR', $error);
					$meta_refresh_time = ($success) ? 3 : 20;
					//$this->notification_helper->notify_album($album_id, $this->user->data['user_id']);
					$data = array(
						'targets'	=> array($this->user->data['user_id']),
						'album_id'	=> (int) $album_id,
						'last_image'	=> end($process->images),
					);
					$this->notification_helper->new_image($data);
				}
				else
				{
					$target = array(
						'album_id'	=>	(int) $album_id,
						'last_image'	=> end($process->images),
						'uploader'		=> $this->user->data['user_id'],
					);
					$this->notification_helper->notify('approval', $target);
					$message .= (!$error) ? $this->user->lang['ALBUM_UPLOAD_NEED_APPROVAL'] : $this->user->lang('ALBUM_UPLOAD_NEED_APPROVAL_ERROR', $error);
					$meta_refresh_time = 20;
				}
				$message .= '<br /><br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

				// ToDo - notifications!!!
				//$phpbb_gallery_notification->send_notification('album', $album_id, $image_names[0]);

				$this->image->handle_counter($process->images, true);
				$this->album->update_info($album_id);

				$this->url->meta_refresh($meta_refresh_time, $album_backlink);
				trigger_error($message);
			}

			$num_images = 0;
			foreach ($process->images as $image_id)
			{
				$data = $process->image_data[$image_id];
				$this->template->assign_block_vars('image', array(
					'U_IMAGE'		=> $this->image->generate_link('thumbnail', 'plugin', $image_id, $data['image_name'], $album_id),
					'IMAGE_NAME'	=> $data['image_name'],
					'IMAGE_DESC'	=> $data['image_desc'],
				));
				$num_images++;
			}

			$s_hidden_fields = build_hidden_fields(array(
				'upload_ids'	=> $process->generate_hidden_fields(),
			));

			$s_can_rotate = ($this->gallery_config->get('allow_rotate') && function_exists('imagerotate'));
			$this->template->assign_vars(array(
				'ERROR'				=> $error,
				'S_UPLOAD_EDIT'		=> true,
				'S_ALLOW_ROTATE'	=> $s_can_rotate,
				'S_ALBUM_ACTION'		=>  $this->helper->route('phpbbgallery_core_album_upload', array('album_id' => $album_id)),
				'S_USERNAME'		=> (!$this->user->data['is_registered']) ? $username : '',
				'NUM_IMAGES'		=> $num_images,
				'COLOUR_ROWSPAN'	=> ($s_can_rotate) ? $num_images * 3 : $num_images * 2,

				'L_DESCRIPTION_LENGTH'	=> $this->user->lang('DESCRIPTION_LENGTH', $this->gallery_config->get('description_length')),
				'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
			));
		}
		return $this->helper->render('gallery/posting_body.html', $page_title);
	}

	private function check_fs()
	{

		$phpbbgallery_core_file = $this->phpbb_root_path . 'files/phpbbgallery/core';
		$phpbbgallery_core_file_medium = $this->phpbb_root_path . 'files/phpbbgallery/core/medium';
		$phpbbgallery_core_file_mini = $this->phpbb_root_path . 'files/phpbbgallery/core/mini';
		$phpbbgallery_core_file_source = $this->phpbb_root_path . 'files/phpbbgallery/core/source';

		if (file_exists($phpbbgallery_core_file) && is_writable($phpbbgallery_core_file) && file_exists($phpbbgallery_core_file_source) && is_writable($phpbbgallery_core_file_source) && file_exists($phpbbgallery_core_file_medium) && is_writable($phpbbgallery_core_file_medium) && file_exists($phpbbgallery_core_file_mini) && is_writable($phpbbgallery_core_file_mini))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
