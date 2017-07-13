<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

class comment
{
	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbbgallery\core\image\image */
	protected $image;

	/* @var \phpbbgallery\core\album\loader */
	protected $loader;

	/* @var \phpbbgallery\core\album\album */
	protected $album;

	/* @var \phpbbgallery\core\album\display */
	protected $display;

	/* @var \phpbbgallery\core\url */
	protected $url;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\user $user
	 * @param \phpbb\auth\auth $auth
	 * @param \phpbb\config\config $config
	 * @param \phpbb\template\template $template
	 * @param \phpbb\request\request $request phpBB request class
	 * @param \phpbb\controller\helper $helper Controller helper object
	 * @param \phpbbgallery\core\image\image $image phpBB Gallery Core image object
	 * @param \phpbbgallery\core\album\loader $loader phpBB Gallery Core album loader
	 * @param \phpbbgallery\core\album\album $album phpBB Gallery Core album object
	 * @param \phpbbgallery\core\album\display $display phpBB Gallery Core album display
	 * @param \phpbbgallery\core\url $url phpBB Gallery Core url object
	 * @param \phpbbgallery\core\auth\auth $gallery_auth
	 * @param \phpbbgallery\core\config $gallery_config
	 * @param \phpbbgallery\core\misc $misc
	 * @param \phpbbgallery\core\comment $comment
	 * @param \phpbbgallery\core\user $gallery_user
	 * @param \phpbbgallery\core\log $gallery_log
	 * @param \phpbbgallery\core\notification\helper $notification_helper
	 * @param \phpbbgallery\core\notification $gallery_notification
	 * @param \phpbbgallery\core\rating $gallery_rating
	 * @param ContainerInterface $phpbb_container
	 * @param                                        $table_comments
	 * @param                                        $phpbb_root_path
	 * @param                                        $php_ext
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\template\template $template,
\phpbb\request\request $request, \phpbb\controller\helper $helper, \phpbbgallery\core\image\image $image, \phpbbgallery\core\album\loader $loader, \phpbbgallery\core\album\album $album,
\phpbbgallery\core\album\display $display, \phpbbgallery\core\url $url, \phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\config $gallery_config, \phpbbgallery\core\misc $misc,
\phpbbgallery\core\comment $comment, \phpbbgallery\core\user $gallery_user, \phpbbgallery\core\log $gallery_log, \phpbbgallery\core\notification\helper $notification_helper,
	\phpbbgallery\core\notification $gallery_notification, \phpbbgallery\core\rating $gallery_rating, ContainerInterface $phpbb_container,
$table_comments, $phpbb_root_path, $php_ext)
	{
		$this->db = $db;
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->helper = $helper;
		$this->image = $image;
		$this->loader = $loader;
		$this->album = $album;
		$this->display = $display;
		$this->url = $url;
		$this->gallery_auth = $gallery_auth;
		$this->gallery_config = $gallery_config;
		$this->misc = $misc;
		$this->comment = $comment;
		$this->gallery_user = $gallery_user;
		$this->gallery_log = $gallery_log;
		$this->notification_helper = $notification_helper;
		$this->gallery_notification = $gallery_notification;
		$this->gallery_rating = $gallery_rating;
		$this->phpbb_container = $phpbb_container;
		$this->table_comments = $table_comments;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * comment Controller
	 *    Route: gallery/comment/{image_id}/add
	 *
	 * @param int $image_id Image ID
	 * @param $comment_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function add($image_id, $comment_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		add_form_key('gallery');
		if ($comment_id != 0)
		{
			$sql = 'SELECT *
				FROM ' . $this->table_comments . '
				WHERE comment_id = ' . (int) $comment_id;
			$result = $this->db->sql_query($sql);
			$comment_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$image_id = (int) $comment_data['comment_image_id'];
		}

		$submit = $this->request->variable('submit', false);
		$error = $message = '';
		// load Image Data
		$image_data = $this->image->get_image_data($image_id);
		$album_id = (int) $image_data['image_album_id'];
		$album_data = $this->loader->get($album_id);
		$this->display->generate_navigation($album_data);
		$page_title = $image_data['image_name'];

		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id));
		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id));
		$album_loginlink = append_sid($this->phpbb_root_path . 'ucp.' . $this->php_ext . '?mode=login');

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('c_post', $album_id, $album_data['album_user_id']))
		{
			$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}

		add_form_key('gallery');
		$this->user->add_lang('posting');

		if (!function_exists('generate_smilies'))
		{
			include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		}
		if (!function_exists('display_custom_bbcodes'))
		{
			include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		}

		$bbcode_status	= ($this->config['allow_bbcode']) ? true : false;
		$smilies_status	= ($this->config['allow_smilies']) ? true : false;
		$img_status		= ($bbcode_status) ? true : false;
		$url_status		= ($this->config['allow_post_links']) ? true : false;
		$flash_status	= false;
		$quote_status	= true;

		// Build custom bbcodes array
		display_custom_bbcodes();

		// Build smilies array
		generate_smilies('inline', 0);

		//$s_hide_comment_input = (time() < ($album_data['contest_start'] + $album_data['contest_end'])) ? true : false;
		$s_hide_comment_input = false;

		$this->template->assign_vars(array(
			'BBCODE_STATUS'			=> ($bbcode_status) ? sprintf($this->user->lang('BBCODE_IS_ON'), '<a href="' . $this->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>') : sprintf($this->user->lang['BBCODE_IS_OFF'], '<a href="' . $this->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>'),
			'IMG_STATUS'			=> ($img_status) ? $this->user->lang['IMAGES_ARE_ON'] : $this->user->lang['IMAGES_ARE_OFF'],
			'FLASH_STATUS'			=> ($flash_status) ? $this->user->lang['FLASH_IS_ON'] : $this->user->lang['FLASH_IS_OFF'],
			'SMILIES_STATUS'		=> ($smilies_status) ? $this->user->lang['SMILIES_ARE_ON'] : $this->user->lang['SMILIES_ARE_OFF'],
			'URL_STATUS'			=> ($bbcode_status && $url_status) ? $this->user->lang['URL_IS_ON'] : $this->user->lang['URL_IS_OFF'],

			'S_BBCODE_ALLOWED'			=> $bbcode_status,
			'S_SMILIES_ALLOWED'			=> $smilies_status,
			'S_LINKS_ALLOWED'			=> $url_status,
			'S_BBCODE_IMG'			=> $img_status,
			'S_BBCODE_URL'			=> $url_status,
			'S_BBCODE_FLASH'		=> $flash_status,
			'S_BBCODE_QUOTE'		=> $quote_status,
		));

		if ($this->misc->display_captcha('comment'))
		{
			$captcha = $this->phpbb_container->get('captcha.factory')->get_instance($this->config['captcha_plugin']);
			$captcha->init(CONFIRM_POST);
			$s_captcha_hidden_fields = '';
			$this->template->assign_vars(array(
				'S_CONFIRM_CODE'		=> true,
				'CAPTCHA_TEMPLATE'		=> $captcha->get_template(),
			));

		}

		$s_captcha_hidden_fields = '';
		$comment_username_req = ($this->user->data['user_id'] == ANONYMOUS);

		if ($submit)
		{
			if (!check_form_key('gallery'))
			{
				trigger_error('FORM_INVALID');
			}
			if ($this->misc->display_captcha('comment'))
			{
				$captcha_error = $captcha->validate();
				if ($captcha_error)
				{
					$error .= (($error) ? '<br />' : '') . $captcha_error;
				}
			}

			$comment_plain = $this->request->variable('message', '', true);
			$comment_username = $this->request->variable('username', '', true);

			if ($comment_username_req)
			{
				if (!function_exists('validate_username'))
				{
					include_once($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);
				}

				if ($comment_username == '')
				{
					$error .= (($error) ? '<br />' : '') . $this->user->lang['MISSING_USERNAME'];
				}
				if ($result = validate_username($comment_username))
				{
					$this->user->add_lang('ucp');
					$error .= (($error) ? '<br />' : '') . $this->user->lang[$result . '_USERNAME'];
					$submit = false;
				}
			}
			if (($comment_plain == '') && !$s_user_rated)
			{
				$error .= (($error) ? '<br />' : '') . $this->user->lang['MISSING_COMMENT'];
			}
			if (utf8_strlen($comment_plain) > $this->gallery_config->get('comment_length'))
			{
				$error .= (($error) ? '<br />' : '') . $this->user->lang['COMMENT_TOO_LONG'];
			}

			if (!class_exists('bbcode'))
			{
				include($this->phpbb_root_path . 'includes/bbcode.' . $this->php_ext);
			}
			if (!class_exists('parse_message'))
			{
				include_once($this->phpbb_root_path . 'includes/message_parser.' . $this->php_ext);
			}

			$message_parser = new \parse_message();
			$message_parser->message	= utf8_normalize_nfc($comment_plain);
			if ($message_parser->message)
			{
				$message_parser->parse(true, true, true, true, false, true, true, true);
			}
			$sql_ary = array(
				'comment_image_id'		=> (int) $image_id,
				'comment'				=> $message_parser->message,
				'comment_uid'			=> $message_parser->bbcode_uid,
				'comment_bitfield'		=> $message_parser->bbcode_bitfield,
				'comment_signature'		=> ($this->auth->acl_get('u_sig') && isset($_POST['attach_sig'])),
			);
			if ((!$error) && ($sql_ary['comment'] != ''))
			{
				if ($this->misc->display_captcha('comment'))
				{
					$captcha->reset();
				}

				$comment_post_id = $this->comment->add($sql_ary, $comment_username);
				if ($this->gallery_user->get_data('watch_com'))
				{
					$this->gallery_notification->add($image_id);
				}
				$data = array(
					'image_id'	=> (int) $image_id,
					'comment_id'	=> $comment_post_id,
					'poster_id'		=> $this->user->data['user_id'],
				);
				$this->notification_helper->notify('new_comment', $data);
				//$phpbb_gallery_notification->send_notification('image', $image_id, $image_data['image_name']);
				$message .= $this->user->lang['COMMENT_STORED'] . '<br />';
			}
			else if ($this->misc->display_captcha('comment'))
			{
				$s_captcha_hidden_fields = ($captcha->is_solved()) ? build_hidden_fields($captcha->get_hidden_fields()) : '';
			}
			$sig_checked = ($this->auth->acl_get('u_sig') && isset($_POST['attach_sig']));
		}
		else
		{
			if ($comment_id != 0)
			{
				$comment_ary = generate_text_for_edit($comment_data['comment'], $comment_data['comment_uid'], $comment_data['comment_bitfield']);
				$comment_plain = '[quote="' . $comment_data['comment_username'] . '"]' . $comment_ary['text'] . '[/quote]';
			}
			$sig_checked = $this->user->optionget('attachsig');
		}

		$preview = $this->request->variable('preview', false);
		if ($preview)
		{
			$comment_plain = $this->request->variable('message', '', true);
		}

		if ($this->misc->display_captcha('comment'))
		{
			if (!$submit || !$captcha->is_solved())
			{
				$this->template->assign_vars(array(
					'S_CONFIRM_CODE'			=> true,
					'CAPTCHA_TEMPLATE'			=> $captcha->get_template(),
				));
			}
			$this->template->assign_vars(array(
				'S_CAPTCHA_HIDDEN_FIELDS'	=> $s_captcha_hidden_fields,
			));
		}
		$this->template->assign_vars(array(
			'ERROR'					=> $error,
			'MESSAGE'				=> (isset($comment_plain)) ? $comment_plain : '',
			'USERNAME'				=> (isset($comment_username)) ? $comment_username : '',
			'REQ_USERNAME'			=> (!empty($comment_username_req)) ? true : false,
			'L_COMMENT_LENGTH'		=> sprintf($this->user->lang['COMMENT_LENGTH'], $this->gallery_config->get('comment_length')),

			'IMAGE_RSZ_WIDTH'		=> $this->gallery_config->get('medium_width'),
			'IMAGE_RSZ_HEIGHT'		=> $this->gallery_config->get('medium_height'),
			'U_IMAGE'				=> append_sid($this->url->path('full') . 'image/' . (int) $image_id . '/medium'),
			'U_VIEW_IMAGE'			=> append_sid($this->url->path('full') . 'image/' . (int) $image_id),
			'IMAGE_NAME'			=> $image_data['image_name'],

			'S_SIGNATURE_CHECKED'	=> (isset($sig_checked) && $sig_checked) ? ' checked="checked"' : '',
			'S_ALBUM_ACTION'		=> $this->helper->route('phpbbgallery_core_comment_add', array('image_id' => (int) $image_id, 'comment_id' => 0)),
			//'S_ALBUM_ACTION'		=> append_sid($this->url->path('full') . 'comment/' . $image_id . '/add/0'),
		));

		if ($submit && !$error)
		{
			$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
			$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

			$this->url->meta_refresh(3, $image_backlink);
			trigger_error($message);
		}

		return $this->helper->render('gallery/comment_body.html', $page_title);
	}

	/**
	 * comment Controller
	 *    Route: gallery/comment/{image_id}/edit/{comment_id}
	 *
	 * @param int $image_id Image ID
	 * @param $comment_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function edit($image_id, $comment_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		add_form_key('gallery');

		$submit = $this->request->variable('submit', false);
		$error = $message = '';
		// load Image Data
		$image_data = $this->image->get_image_data($image_id);
		$album_id = (int) $image_data['image_album_id'];
		$album_data = $this->loader->get($album_id);
		$this->display->generate_navigation($album_data);
		$page_title = $image_data['image_name'];

		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id));
		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id));
		$image_loginlink = $this->url->append_sid('relative', 'image_page', "album_id=$album_id&amp;image_id=$image_id");
		$album_loginlink = append_sid($this->phpbb_root_path . 'ucp.' . $this->php_ext . '?mode=login');
		if ($comment_id != 0)
		{
			$sql = 'SELECT *
				FROM ' . $this->table_comments . '
				WHERE comment_id = ' . (int) $comment_id;
			$result = $this->db->sql_query($sql);
			$comment_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$image_id = (int) $comment_data['comment_image_id'];
		}
		else
		{
			$this->misc->not_authorised($image_backlink, $image_loginlink);
		}
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('c_edit', $album_id, $album_data['album_user_id']) && $mode == 'add')
		{
			if (!$this->gallery_auth->acl_check('m_comments', $album_id, $album_data['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		else if (($comment_data['comment_user_id'] != $this->user->data['user_id']) && !$this->gallery_auth->acl_check('m_comments', $album_id, $album_data['album_user_id']))
		{
			$this->misc->not_authorised($image_backlink, $image_loginlink);
		}

		$this->user->add_lang('posting');

		if (!function_exists('generate_smilies'))
		{
			include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		}

		$bbcode_status	= ($this->config['allow_bbcode']) ? true : false;
		$smilies_status	= ($this->config['allow_smilies']) ? true : false;
		$img_status		= ($bbcode_status) ? true : false;
		$url_status		= ($this->config['allow_post_links']) ? true : false;
		$flash_status	= false;
		$quote_status	= true;

		if (!function_exists('display_custom_bbcodes'))
		{
			include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		}

		// Build custom bbcodes array
		display_custom_bbcodes();

		// Build smilies array
		generate_smilies('inline', 0);

		//$s_hide_comment_input = (time() < ($album_data['contest_start'] + $album_data['contest_end'])) ? true : false;
		$s_hide_comment_input = false;

		$this->template->assign_vars(array(
			'BBCODE_STATUS'			=> ($bbcode_status) ? sprintf($this->user->lang['BBCODE_IS_ON'], '<a href="' . $this->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>') : sprintf($this->user->lang['BBCODE_IS_OFF'], '<a href="' . $this->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>'),
			'IMG_STATUS'			=> ($img_status) ? $this->user->lang['IMAGES_ARE_ON'] : $this->user->lang['IMAGES_ARE_OFF'],
			'FLASH_STATUS'			=> ($flash_status) ? $this->user->lang['FLASH_IS_ON'] : $this->user->lang['FLASH_IS_OFF'],
			'SMILIES_STATUS'		=> ($smilies_status) ? $this->user->lang['SMILIES_ARE_ON'] : $this->user->lang['SMILIES_ARE_OFF'],
			'URL_STATUS'			=> ($bbcode_status && $url_status) ? $this->user->lang['URL_IS_ON'] : $this->user->lang['URL_IS_OFF'],

			'S_BBCODE_ALLOWED'			=> $bbcode_status,
			'S_SMILIES_ALLOWED'			=> $smilies_status,
			'S_LINKS_ALLOWED'			=> $url_status,
			'S_BBCODE_IMG'			=> $img_status,
			'S_BBCODE_URL'			=> $url_status,
			'S_BBCODE_FLASH'		=> $flash_status,
			'S_BBCODE_QUOTE'		=> $quote_status,
		));

		$comment_username_req = ($comment_data['comment_user_id'] == ANONYMOUS) ? true : false;

		if ($submit)
		{
			if (!check_form_key('gallery'))
			{
				trigger_error('FORM_INVALID');
			}

			$sql_ary = array();
			$comment_plain = $this->request->variable('message', '', true);

			if ($comment_username_req)
			{
				$comment_username = $this->request->variable('username', '');
				if ($comment_username == '')
				{
					$error .= (($error) ? '<br />' : '') . $this->this->user->lang['MISSING_USERNAME'];
				}

				if (validate_username($comment_username))
				{
					$error .= (($error) ? '<br />' : '') . $this->user->lang['INVALID_USERNAME'];
					$comment_username = '';
				}

				$sql_ary = array(
					'comment_username'	=> $comment_username,
				);
			}

			if ($comment_plain == '')
			{
				$error .= (($error) ? '<br />' : '') . $this->user->lang['MISSING_COMMENT'];
			}
			if (utf8_strlen($comment_plain) > $this->gallery_config->get('comment_length'))
			{
				$error .= (($error) ? '<br />' : '') . $this->user->lang['COMMENT_TOO_LONG'];
			}
			if (!class_exists('bbcode'))
			{
				include($this->phpbb_root_path . 'includes/bbcode.' . $this->php_ext);
			}
			if (!class_exists('parse_message'))
			{
				include_once($this->phpbb_root_path . 'includes/message_parser.' . $this->php_ext);
			}
			$message_parser = new \parse_message();
			$message_parser->message = utf8_normalize_nfc($comment_plain);
			if ($message_parser->message)
			{
				$message_parser->parse(true, true, true, true, false, true, true, true);
			}

			$sql_ary = array_merge($sql_ary, array(
				'comment'				=> $message_parser->message,
				'comment_uid'			=> $message_parser->bbcode_uid,
				'comment_bitfield'		=> $message_parser->bbcode_bitfield,
				'comment_edit_count'	=> $comment_data['comment_edit_count'] + 1,
				'comment_signature'		=> ($this->auth->acl_get('u_sig') && isset($_POST['attach_sig'])),
			));

			if (!$error)
			{
				$this->comment->edit($comment_id, $sql_ary);
				$message .= $this->user->lang['COMMENT_STORED'] . '<br />';
				if ($this->user->data['user_id'] != $comment_data['comment_user_id'])
				{
					$this->gallery_log->add_log('moderator', 'c_edit', $image_data['image_album_id'], $image_data['image_id'], array('LOG_GALLERY_COMMENT_EDITED', $image_data['image_name']));
				}
			}
		}
		else
		{
			$sig_checked = (bool) $comment_data['comment_signature'];

			$comment_ary = generate_text_for_edit($comment_data['comment'], $comment_data['comment_uid'], $comment_data['comment_bitfield']);
			$comment_plain = $comment_ary['text'];
			$comment_username = $comment_data['comment_username'];
		}

		$this->template->assign_vars(array(
			'ERROR'					=> $error,
			'MESSAGE'				=> (isset($comment_plain)) ? $comment_plain : '',
			'USERNAME'				=> (isset($comment_username)) ? $comment_username : '',
			'REQ_USERNAME'			=> (!empty($comment_username_req)) ? true : false,
			'L_COMMENT_LENGTH'		=> sprintf($this->user->lang['COMMENT_LENGTH'], $this->gallery_config->get('comment_length')),

			'IMAGE_RSZ_WIDTH'		=> $this->gallery_config->get('medium_width'),
			'IMAGE_RSZ_HEIGHT'		=> $this->gallery_config->get('medium_height'),
			'U_IMAGE'				=> append_sid($this->url->path('full') . 'image/' . (int) $image_id . '/medium'),
			'U_VIEW_IMAGE'			=> append_sid($this->url->path('full') . 'image/' . (int) $image_id),
			'IMAGE_NAME'			=> $image_data['image_name'],

			'S_SIGNATURE_CHECKED'	=> (isset($sig_checked) && $sig_checked) ? ' checked="checked"' : '',
			'S_ALBUM_ACTION'		=> $this->helper->route('phpbbgallery_core_comment_edit', array('image_id' => (int) $image_id, 'comment_id' => (int) $comment_id)),
			//'S_ALBUM_ACTION'		=> append_sid($this->url->path('full') . 'comment/' . $image_id . '/edit/'. $comment_id),
		));

		if ($submit && !$error)
		{
			$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
			$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

			$this->url->meta_refresh(3, $image_backlink);
			trigger_error($message);
		}

		return $this->helper->render('gallery/comment_body.html', $page_title);
	}

	/**
	 * comment Controller
	 *    Route: gallery/comment/{image_id}/delete/{comment_id}
	 *
	 * @param int $image_id Image ID
	 * @param $comment_id
	 * @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function delete($image_id, $comment_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		add_form_key('gallery');

		$submit = $this->request->variable('submit', false);
		$error = $message = '';
		// load Image Data
		$image_data = $this->image->get_image_data($image_id);
		$album_id = (int) $image_data['image_album_id'];
		$album_data = $this->loader->get($album_id);
		$this->display->generate_navigation($album_data);
		$page_title = $image_data['image_name'];

		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id));
		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id));
		$image_loginlink = $this->url->append_sid('relative', 'image_page', "album_id=$album_id&amp;image_id=$image_id");
		$album_loginlink = append_sid($this->phpbb_root_path . 'ucp.' . $this->php_ext . '?mode=login');
		if ($comment_id != 0)
		{
			$sql = 'SELECT *
				FROM ' . $this->table_comments . '
				WHERE comment_id = ' . (int) $comment_id;
			$result = $this->db->sql_query($sql);
			$comment_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$image_id = (int) $comment_data['comment_image_id'];
		}
		else
		{
			$this->misc->not_authorised($image_backlink, $image_loginlink);
		}
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if (!$this->gallery_auth->acl_check('c_edit', $album_id, $album_data['album_user_id']) && $mode == 'add')
		{
			if (!$this->gallery_auth->acl_check('m_comments', $album_id, $album_data['album_user_id']))
			{
				$this->misc->not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
			}
		}
		else if (($comment_data['comment_user_id'] != $this->user->data['user_id']) && !$this->gallery_auth->acl_check('m_comments', $album_id, $album_data['album_user_id']))
		{
			$this->misc->not_authorised($image_backlink, $image_loginlink);
		}

		$this->user->add_lang('posting');

		if (!function_exists('generate_smilies'))
		{
			include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		}

		$bbcode_status	= ($this->config['allow_bbcode']) ? true : false;
		$smilies_status	= ($this->config['allow_smilies']) ? true : false;
		$img_status		= ($bbcode_status) ? true : false;
		$url_status		= ($this->config['allow_post_links']) ? true : false;
		$flash_status	= false;
		$quote_status	= true;

		if (!function_exists('display_custom_bbcodes'))
		{
			include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		}

		// Build custom bbcodes array
		display_custom_bbcodes();

		// Build smilies array
		generate_smilies('inline', 0);

		//$s_hide_comment_input = (time() < ($album_data['contest_start'] + $album_data['contest_end'])) ? true : false;
		$s_hide_comment_input = false;

		$this->template->assign_vars(array(
			'BBCODE_STATUS'			=> ($bbcode_status) ? sprintf($this->user->lang['BBCODE_IS_ON'], '<a href="' . $this->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>') : sprintf($this->user->lang['BBCODE_IS_OFF'], '<a href="' . $this->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>'),
			'IMG_STATUS'			=> ($img_status) ? $this->user->lang['IMAGES_ARE_ON'] : $this->user->lang['IMAGES_ARE_OFF'],
			'FLASH_STATUS'			=> ($flash_status) ? $this->user->lang['FLASH_IS_ON'] : $this->user->lang['FLASH_IS_OFF'],
			'SMILIES_STATUS'		=> ($smilies_status) ? $this->user->lang['SMILIES_ARE_ON'] : $this->user->lang['SMILIES_ARE_OFF'],
			'URL_STATUS'			=> ($bbcode_status && $url_status) ? $this->user->lang['URL_IS_ON'] : $this->user->lang['URL_IS_OFF'],

			'S_BBCODE_ALLOWED'			=> $bbcode_status,
			'S_SMILIES_ALLOWED'			=> $smilies_status,
			'S_LINKS_ALLOWED'			=> $url_status,
			'S_BBCODE_IMG'			=> $img_status,
			'S_BBCODE_URL'			=> $url_status,
			'S_BBCODE_FLASH'		=> $flash_status,
			'S_BBCODE_QUOTE'		=> $quote_status,
		));

		$s_hidden_fields = build_hidden_fields(array(
			'album_id'		=> $album_id,
			'image_id'		=> $image_id,
			'comment_id'	=> $comment_id,
			'mode'			=> 'delete',
		));

		if (confirm_box(true))
		{
			$this->comment->delete_comments($comment_id);
			if ($this->user->data['user_id'] != $comment_data['comment_user_id'])
			{
				$this->gallery_log->add_log('moderator', 'c_delete', $image_data['image_album_id'], $image_data['image_id'], array('LOG_GALLERY_COMMENT_DELETED', $image_data['image_name']));
			}

			$message = $this->user->lang['DELETED_COMMENT'] . '<br />';
			$submit = true;
		}
		else
		{
			if (isset($_POST['cancel']))
			{
				$message = $this->user->lang['DELETED_COMMENT_NOT'] . '<br />';
				$submit = true;
			}
			else
			{
				confirm_box(false, 'DELETE_COMMENT2', $s_hidden_fields);
			}
		}

		$this->template->assign_vars(array(
			'ERROR'					=> $error,
			'MESSAGE'				=> (isset($comment_plain)) ? $comment_plain : '',
			'USERNAME'				=> (isset($comment_username)) ? $comment_username : '',
			'REQ_USERNAME'			=> (!empty($comment_username_req)) ? true : false,
			'L_COMMENT_LENGTH'		=> sprintf($this->user->lang['COMMENT_LENGTH'], $this->gallery_config->get('comment_length')),

			'IMAGE_RSZ_WIDTH'		=> $this->gallery_config->get('medium_width'),
			'IMAGE_RSZ_HEIGHT'		=> $this->gallery_config->get('medium_height'),
			'U_IMAGE'				=> append_sid($this->url->path('full') . 'image/' . (int) $image_id . '/medium'),
			'U_VIEW_IMAGE'			=> append_sid($this->url->path('full') . 'image/' . (int) $image_id),
			'IMAGE_NAME'			=> $image_data['image_name'],

			'S_SIGNATURE_CHECKED'	=> (isset($sig_checked) && $sig_checked) ? ' checked="checked"' : '',
			'S_ALBUM_ACTION'		=> append_sid($this->url->path('full') . 'comment/' . (int) $image_id . '/edit/'. (int) $comment_id),
		));

		if ($submit && !$error)
		{
			$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
			$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

			$this->url->meta_refresh(3, $image_backlink);
			trigger_error($message);
		}

		return $this->helper->render('gallery/comment_body.html', $page_title);
	}

	public function rate($image_id)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		add_form_key('gallery');

		$submit = $this->request->variable('submit', false);
		$error = $message = '';
		// load Image Data
		$image_data = $this->image->get_image_data($image_id);
		$album_id = (int) $image_data['image_album_id'];
		$album_data = $this->loader->get($album_id);
		$this->display->generate_navigation($album_data);
		$page_title = $image_data['image_name'];

		$image_backlink = $this->helper->route('phpbbgallery_core_image', array('image_id' => $image_id));
		$album_backlink = $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_id));
		$image_loginlink = $this->url->append_sid('relative', 'image_page', "album_id=$album_id&amp;image_id=$image_id");

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$rating = $this->gallery_rating;
		$rating->loader($image_id, $image_data, $album_data);
		if (!($this->gallery_config->get('allow_rates') && $rating->is_able()))
		{
			// The user is unable to rate.
			$this->misc->not_authorised($image_backlink, $image_loginlink);
		}

		$this->user->add_lang('posting');

		if (!function_exists('generate_smilies'))
		{
			include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		}

		$bbcode_status	= ($this->config['allow_bbcode']) ? true : false;
		$smilies_status	= ($this->config['allow_smilies']) ? true : false;
		$img_status		= ($bbcode_status) ? true : false;
		$url_status		= ($this->config['allow_post_links']) ? true : false;
		$flash_status	= false;
		$quote_status	= true;

		if (!function_exists('display_custom_bbcodes'))
		{
			include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		}// Build custom bbcodes array
		display_custom_bbcodes();

		// Build smilies array
		generate_smilies('inline', 0);

		/**
		* Rating-System: now you can comment and rate in one form
		*/
		$s_user_rated = false;
		if ($this->gallery_config->get('allow_rates'))
		{
			$user_rating = $rating->get_user_rating($this->user->data['user_id']);

			// Check: User didn't rate yet, has permissions, it's not the users own image and the user is logged in
			if (!$user_rating && $rating->is_allowed())
			{
				$rating->display_box();

				// User just rated the image, so we store it
				$rate_point = $this->request->variable('rating', 0);
				if ($rating->rating_enabled && $rate_point > 0)
				{
					$rating->submit_rating();
					$s_user_rated = true;

					$message .= $this->user->lang['RATING_SUCCESSFUL'] . '<br />';
				}
				$this->template->assign_vars(array(
					'S_ALLOWED_TO_RATE'			=> $rating->is_allowed(),
				));
			}

		}

		$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
		$message .= '<br />' . sprintf($this->user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

		$this->url->meta_refresh(3, $image_backlink);
		trigger_error($message);

		return $this->helper->render('gallery/comment_body.html', $page_title);
	}
}
