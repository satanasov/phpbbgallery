<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include('common.' . $phpEx);
include($phpbb_root_path . 'common.' . $phpEx);

$phpbb_ext_gallery = new phpbb_ext_gallery_core($auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path);
$phpbb_ext_gallery->setup('posting');
$phpbb_ext_gallery->url->_include(array('functions_display', 'functions_posting', 'functions_user'), 'phpbb');
$phpbb_ext_gallery->url->_include(array('bbcode', 'message_parser'), 'phpbb');

$user->add_lang_ext('gallery/core', 'gallery');

add_form_key('gallery');
$submit = (isset($_POST['submit'])) ? true : false;
$mode = request_var('mode', '');
$album_id = request_var('album_id', 0);
$image_id = request_var('image_id', 0);
$error = $message = '';
$slower_redirect = false;

if (!$album_id && ($mode == 'upload'))
{
	// Public albums
	$s_album_select = phpbb_ext_gallery_core_album::get_albumbox(true, '', 0, 'i_upload', false, phpbb_ext_gallery_core_album::PUBLIC_ALBUM, phpbb_ext_gallery_core_album::TYPE_UPLOAD);
	if ($user->data['user_id'] != ANONYMOUS)
	{
		// Personal albums
		$s_album_select .= phpbb_ext_gallery_core_album::get_albumbox(false, '', -1, 'i_upload', false, $user->data['user_id'], phpbb_ext_gallery_core_album::TYPE_UPLOAD);
	}
	$template->assign_vars(array(
	//function get_albumbox($ignore_personals, $select_name, $select_id = false, $requested_permission = false, $ignore_id = false, $album_user_id = self::PUBLIC_ALBUM, $requested_album_type = -1)
		'S_ALBUM_SELECT'	=> $s_album_select,
	));

	page_header($user->lang['UPLOAD_IMAGE'], false);

	$template->set_filenames(array(
		'body' => 'gallery/posting_body.html',
	));

	page_footer();
	return;
}

// Check for permissions cheaters!
if ($image_id)
{
	$image_data = phpbb_ext_gallery_core_image::get_info($image_id);
	$album_id = $image_data['image_album_id'];
}
$album_data = phpbb_ext_gallery_core_album::get_info($album_id);

phpbb_ext_gallery_core_album_display::generate_nav($album_data);

if ($image_id)
{
	$image_backlink = $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id");
	$image_loginlink = $phpbb_ext_gallery->url->append_sid('relative', 'image_page', "album_id=$album_id&amp;image_id=$image_id");
}

$album_backlink = $phpbb_ext_gallery->url->append_sid('album', "album_id=$album_id");
$album_loginlink = $phpbb_ext_gallery->url->append_sid('relative', 'album', "album_id=$album_id");


// Send some cheaters back
if ($user->data['is_bot'])
{
	redirect(($image_id) ? $image_backlink : $album_backlink);
}
if ($album_data['album_type'] == phpbb_ext_gallery_core_album::TYPE_CAT)
{
	// If we get here, the database is corrupted,
	// but at least we dont let them do anything.
	meta_refresh(3, $album_backlink);
	trigger_error('ALBUM_IS_CATEGORY');
}

if ($image_id && (!$phpbb_ext_gallery->auth->acl_check('m_status', $album_id, $album_data['album_user_id']) && ($image_data['image_status'] != phpbb_ext_gallery_core_image::STATUS_APPROVED)))
{
	phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
}
else if (!$phpbb_ext_gallery->auth->acl_check('m_status', $album_id, $album_data['album_user_id']) && ($album_data['album_status'] == phpbb_ext_gallery_core_album::STATUS_LOCKED))
{
	phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
}

switch ($mode)
{
	case 'upload':
	case 'upload_edit':
		if (!$phpbb_ext_gallery->auth->acl_check('i_upload', $album_id, $album_data['album_user_id']) || ($album_data['album_status'] == phpbb_ext_gallery_core_album::STATUS_LOCKED))
		{
			phpbb_ext_gallery_core_misc::not_authorised($album_backlink, $album_loginlink, 'LOGIN_EXPLAIN_UPLOAD');
		}
		if (!phpbb_ext_gallery_core_contest::is_step('upload', $album_data))
		{
			phpbb_ext_gallery_core_misc::not_authorised($album_backlink, $album_loginlink);
		}
	break;

	case 'edit':
	case 'delete':
		if (!$phpbb_ext_gallery->auth->acl_check('i_' . $mode, $album_id, $album_data['album_user_id']))
		{
			if (!$phpbb_ext_gallery->auth->acl_check('m_' . $mode, $album_id, $album_data['album_user_id']))
			{
				phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
			}
		}
		else if (($image_data['image_user_id'] != $user->data['user_id']) && !$phpbb_ext_gallery->auth->acl_check('m_' . $mode, $album_id, $album_data['album_user_id']))
		{
			phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
		}
	break;

	case 'report':
		if (!$phpbb_ext_gallery->auth->acl_check('i_report', $album_id, $album_data['album_user_id']) || ($image_data['image_user_id'] == $user->data['user_id']))
		{
			phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
		}
	break;

	default:
		phpbb_ext_gallery_core_misc::not_authorised($album_backlink, $album_loginlink);
	break;
}

if ($mode == 'report')
{
	if ($submit)
	{
		if (!check_form_key('gallery'))
		{
			trigger_error('FORM_INVALID');
		}

		$report_message = request_var('message', '', true);
		$error = '';
		if ($report_message == '')
		{
			$error = $user->lang['MISSING_REPORT_REASON'];
			$submit = false;
		}

		if (!$error && $image_data['image_reported'])
		{
			$error = $user->lang['IMAGE_ALREADY_REPORTED'];
		}

		if (!$error)
		{
			$data = array(
				'report_album_id'			=> $album_id,
				'report_image_id'			=> $image_id,
				'report_note'				=> $report_message,
			);
			phpbb_gallery_report::add($data);

			$message = $user->lang['IMAGES_REPORTED_SUCCESSFULLY'];
			$message .= '<br /><br />' . sprintf($user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
			$message .= '<br /><br />' . sprintf($user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

			meta_refresh(3, $image_backlink);
			trigger_error($message);
		}

	}

	$template->assign_vars(array(
		'ERROR'				=> $error,
		'U_IMAGE'			=> ($image_id) ? $phpbb_ext_gallery->url->append_sid('image', "album_id=$album_id&amp;image_id=$image_id") : '',
		'U_VIEW_IMAGE'		=> ($image_id) ? $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id") : '',
		'IMAGE_RSZ_WIDTH'	=> $phpbb_ext_gallery->config->get('medium_width'),
		'IMAGE_RSZ_HEIGHT'	=> $phpbb_ext_gallery->config->get('medium_height'),

		'S_REPORT'			=> true,
		'S_ALBUM_ACTION'	=> $phpbb_ext_gallery->url->append_sid('posting', "mode=report&amp;album_id=$album_id&amp;image_id=$image_id"),
	));
}
else if ($mode == 'delete')
{
	$s_hidden_fields = build_hidden_fields(array(
		'album_id'		=> $album_id,
		'image_id'		=> $image_id,
		'mode'			=> 'delete',
	));

	if (confirm_box(true))
	{
		phpbb_ext_gallery_core_image::handle_counter($image_id, false);
		phpbb_ext_gallery_core_image::delete_images(array($image_id), array($image_id => $image_data['image_filename']));
		phpbb_ext_gallery_core_album::update_info($album_id);

		$message = $user->lang['DELETED_IMAGE'] . '<br />';
		$message .= '<br />' . sprintf($user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

		if ($user->data['user_id'] != $image_data['image_user_id'])
		{
			add_log('gallery', $image_data['image_album_id'], $image_id, 'LOG_GALLERY_DELETED', $image_data['image_name']);
		}

		meta_refresh(3, $album_backlink);
		trigger_error($message);
	}
	else
	{
		if (isset($_POST['cancel']))
		{
			$message = $user->lang['DELETED_IMAGE_NOT'] . '<br />';
			$message .= '<br />' . sprintf($user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
			meta_refresh(3, $image_backlink);
			trigger_error($message);
		}
		else
		{
			confirm_box(false, 'DELETE_IMAGE2', $s_hidden_fields);
		}
	}
}
else
{
	if ($mode == 'upload')
	{
		// Upload Quota Check
		// 1. Check album-configuration Quota
		if (($phpbb_ext_gallery->config->get('album_images') >= 0) && ($album_data['album_images'] >= $phpbb_ext_gallery->config->get('album_images')))
		{
			//@todo: Add return link
			trigger_error('ALBUM_REACHED_QUOTA');
		}

		// 2. Check user-limit, if he is not allowed to go unlimited
		if (!$phpbb_ext_gallery->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id']))
		{
			$sql = 'SELECT COUNT(image_id) count
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE image_user_id = ' . $user->data['user_id'] . '
					AND image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
					AND image_album_id = ' . $album_id;
			$result = $db->sql_query($sql);
			$own_images = (int) $db->sql_fetchfield('count');
			$db->sql_freeresult($result);
			if ($own_images >= $phpbb_ext_gallery->auth->acl_check('i_count', $album_id, $album_data['album_user_id']))
			{
				//@todo: Add return link
				trigger_error($user->lang('USER_REACHED_QUOTA', $phpbb_ext_gallery->auth->acl_check('i_count', $album_id, $album_data['album_user_id'])));
			}
		}

		if (phpbb_ext_gallery_core_misc::display_captcha('upload'))
		{
			$phpbb_ext_gallery->url->_include('captcha/captcha_factory', 'phpbb');
			$captcha =& phpbb_captcha_factory::get_instance($config['captcha_plugin']);
			$captcha->init(CONFIRM_POST);
			$s_captcha_hidden_fields = '';
		}

		$upload_files_limit = ($phpbb_ext_gallery->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id'])) ? $phpbb_ext_gallery->config->get('num_uploads') : min(($phpbb_ext_gallery->auth->acl_check('i_count', $album_id, $album_data['album_user_id']) - $own_images), $phpbb_ext_gallery->config->get('num_uploads'));

		if ($submit)
		{
			if (!check_form_key('gallery'))
			{
				trigger_error('FORM_INVALID');
			}

			$process = new phpbb_ext_gallery_core_upload($album_id, $upload_files_limit);
			$process->set_rotating(request_var('rotate', array(0)));
			$process->set_allow_comments(request_var('allow_comments', false));

			if (phpbb_ext_gallery_core_misc::display_captcha('upload'))
			{
				$captcha_error = $captcha->validate();
				if ($captcha_error !== false)
				{
					$process->new_error($captcha_error);
				}
			}

			if (!$user->data['is_registered'])
			{
				$username = request_var('username', $user->data['username']);
				if ($result = validate_username($username))
				{
					$user->add_lang('ucp');
					$error_array[] = $user->lang[$result . '_USERNAME'];
				}
				else
				{
					$process->set_username($username);
				}
			}

			if (empty($process->errors))
			{

				for ($file_count = 0; $file_count < $upload_files_limit; $file_count++)
				{
					/**
					* Upload an image from the FILES-array,
					* call some functions (rotate, resize, ...)
					* and store the image to the database
					*/
					$process->upload_file($file_count);
				}
			}

			if (!$process->uploaded_files)
			{
				$process->new_error($user->lang['UPLOAD_NO_FILE']);
			}
			else
			{
				$mode = 'upload_edit';
				// Remove submit, so we get the first screen of step 2.
				$submit = false;
			}

			$error = implode('<br />', $process->errors);

			if (phpbb_ext_gallery_core_misc::display_captcha('upload'))
			{
				$captcha->reset();
			}
		}

		if (!$submit || (isset($process) && !$process->uploaded_files))
		{
			for ($i = 0; $i < $upload_files_limit; $i++)
			{
				$template->assign_block_vars('upload_image', array());
			}
		}

		if ($mode == 'upload')
		{
			$template->assign_vars(array(
				'ERROR'					=> $error,
				'S_MAX_FILESIZE'		=> get_formatted_filesize($phpbb_ext_gallery->config->get('max_filesize')),
				'S_MAX_WIDTH'			=> $phpbb_ext_gallery->config->get('max_width'),
				'S_MAX_HEIGHT'			=> $phpbb_ext_gallery->config->get('max_height'),
				'S_ALLOWED_FILETYPES'	=> implode(', ', phpbb_ext_gallery_core_upload::get_allowed_types(true)),
				'S_ALBUM_ACTION'		=> $phpbb_ext_gallery->url->append_sid('posting', "mode=upload&amp;album_id=$album_id"),
				'S_UPLOAD'				=> true,
				'S_ALLOW_ROTATE'		=> ($phpbb_ext_gallery->config->get('allow_rotate') && function_exists('imagerotate')),
				'S_UPLOAD_LIMIT'		=> $upload_files_limit,

				'S_COMMENTS_ENABLED'	=> $phpbb_ext_gallery->config->get('allow_comments') && $phpbb_ext_gallery->config->get('comment_user_control'),
				'S_ALLOW_COMMENTS'		=> true,
				'L_ALLOW_COMMENTS'		=> $user->lang('ALLOW_COMMENTS_ARY', $upload_files_limit),
			));

			if (phpbb_ext_gallery_core_misc::display_captcha('upload'))
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
			}
		}
	}

	if ($mode != 'upload')
	{
		// Load BBCodes and smilies data
		$bbcode_status	= ($config['allow_bbcode']) ? true : false;
		$smilies_status	= ($config['allow_smilies']) ? true : false;
		$img_status		= ($bbcode_status) ? true : false;
		$url_status		= ($config['allow_post_links']) ? true : false;
		$flash_status	= false;
		$quote_status	= true;

		$template->assign_vars(array(
			'BBCODE_STATUS'			=> ($bbcode_status) ? sprintf($user->lang['BBCODE_IS_ON'], '<a href="' . $phpbb_ext_gallery->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>') : sprintf($user->lang['BBCODE_IS_OFF'], '<a href="' . $phpbb_ext_gallery->url->append_sid('phpbb', 'faq', 'mode=bbcode') . '">', '</a>'),
			'IMG_STATUS'			=> ($img_status) ? $user->lang['IMAGES_ARE_ON'] : $user->lang['IMAGES_ARE_OFF'],
			'FLASH_STATUS'			=> ($flash_status) ? $user->lang['FLASH_IS_ON'] : $user->lang['FLASH_IS_OFF'],
			'SMILIES_STATUS'		=> ($smilies_status) ? $user->lang['SMILIES_ARE_ON'] : $user->lang['SMILIES_ARE_OFF'],
			'URL_STATUS'			=> ($bbcode_status && $url_status) ? $user->lang['URL_IS_ON'] : $user->lang['URL_IS_OFF'],

			'S_BBCODE_ALLOWED'		=> $bbcode_status,
			'S_SMILIES_ALLOWED'		=> $smilies_status,
			'S_LINKS_ALLOWED'		=> $url_status,
			'S_BBCODE_IMG'			=> $img_status,
			'S_BBCODE_URL'			=> $url_status,
			'S_BBCODE_FLASH'		=> $flash_status,
			'S_BBCODE_QUOTE'		=> $quote_status,
		));

		// Build custom bbcodes array
		display_custom_bbcodes();

		// Build smilies array
		generate_smilies('inline', 0);
	}

	if ($mode == 'upload_edit')
	{
		if ($submit)
		{
			// Upload Quota Check
			// 1. Check album-configuration Quota
			if (($phpbb_ext_gallery->config->get('album_images') >= 0) && ($album_data['album_images'] >= $phpbb_ext_gallery->config->get('album_images')))
			{
				//@todo: Add return link
				trigger_error('ALBUM_REACHED_QUOTA');
			}

			// 2. Check user-limit, if he is not allowed to go unlimited
			if (!$phpbb_ext_gallery->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id']))
			{
				$sql = 'SELECT COUNT(image_id) count
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE image_user_id = ' . $user->data['user_id'] . '
						AND image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND image_album_id = ' . $album_id;
				$result = $db->sql_query($sql);
				$own_images = (int) $db->sql_fetchfield('count');
				$db->sql_freeresult($result);
				if ($own_images >= $phpbb_ext_gallery->auth->acl_check('i_count', $album_id, $album_data['album_user_id']))
				{
					//@todo: Add return link
					trigger_error($user->lang('USER_REACHED_QUOTA', $phpbb_ext_gallery->auth->acl_check('i_count', $album_id, $album_data['album_user_id'])));
				}
			}

			$upload_files_limit = ($phpbb_ext_gallery->auth->acl_check('i_unlimited', $album_id, $album_data['album_user_id'])) ? $phpbb_ext_gallery->config->get('num_uploads') : min(($phpbb_ext_gallery->auth->acl_check('i_count', $album_id, $album_data['album_user_id']) - $own_images), $phpbb_ext_gallery->config->get('num_uploads'));

			$upload_ids = request_var('upload_ids', array(''));

			$process = new phpbb_ext_gallery_core_upload($album_id, $upload_files_limit);
			$process->set_rotating(request_var('rotate', array(0)));
			$process->get_images($upload_ids);
			$image_names = request_var('image_name', array(''), true);
			$process->set_names($image_names);
			$process->set_descriptions(request_var('message', array(''), true));
			$process->set_image_num(request_var('image_num', 0));
			$process->use_same_name(request_var('same_name', false));

			$success = true;
			foreach ($process->images as $image_id)
			{
				$success = $success && $process->update_image($image_id, !$phpbb_ext_gallery->auth->acl_check('i_approve', $album_id, $album_data['album_user_id']), $album_data['album_contest']);
				if ($phpbb_ext_gallery->user->get_data('watch_own'))
				{
					phpbb_ext_gallery_core_notification::add($image_id);
				}
			}

			$message = '';
			$error = implode('<br />', $process->errors);
			if ($phpbb_ext_gallery->auth->acl_check('i_approve', $album_id, $album_data['album_user_id']))
			{
				$message .= (!$error) ? $user->lang['ALBUM_UPLOAD_SUCCESSFUL'] : $user->lang('ALBUM_UPLOAD_SUCCESSFUL_ERROR', $error);
				$meta_refresh_time = ($success) ? 3 : 20;
			}
			else
			{
				$message .= (!$error) ? $user->lang['ALBUM_UPLOAD_NEED_APPROVAL'] : $user->lang('ALBUM_UPLOAD_NEED_APPROVAL_ERROR', $error);
				$meta_refresh_time = 20;
			}
			$message .= '<br /><br />' . sprintf($user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

			phpbb_ext_gallery_core_notification::send_notification('album', $album_id, $image_names[0]);
			phpbb_ext_gallery_core_image::handle_counter($process->images, true);
			phpbb_ext_gallery_core_album::update_info($album_id);

			meta_refresh($meta_refresh_time, $album_backlink);
			trigger_error($message);
		}

		$num_images = 0;
		foreach ($process->images as $image_id)
		{
			$data = $process->image_data[$image_id];
			$template->assign_block_vars('image', array(
				'U_IMAGE'		=> phpbb_ext_gallery_core_image::generate_link('thumbnail', 'plugin', $image_id, $data['image_name'], $album_id),
				'IMAGE_NAME'	=> $data['image_name'],
				'IMAGE_DESC'	=> $data['image_desc'],
			));
			$num_images++;
		}

		$s_hidden_fields = build_hidden_fields(array(
			'upload_ids'	=> $process->generate_hidden_fields(),
		));

		$s_can_rotate = ($phpbb_ext_gallery->config->get('allow_rotate') && function_exists('imagerotate'));
		$template->assign_vars(array(
			'ERROR'				=> $error,
			'S_ALBUM_ACTION'	=> $phpbb_ext_gallery->url->append_sid('posting', "mode=upload_edit&amp;album_id=$album_id"),
			'S_UPLOAD_EDIT'		=> true,
			'S_ALLOW_ROTATE'	=> $s_can_rotate,

			'S_USERNAME'		=> (!$user->data['is_registered']) ? $username : '',
			'NUM_IMAGES'		=> $num_images,
			'COLOUR_ROWSPAN'	=> ($s_can_rotate) ? $num_images * 3 : $num_images * 2,

			'L_DESCRIPTION_LENGTH'	=> $user->lang('DESCRIPTION_LENGTH', $phpbb_ext_gallery->config->get('description_length')),
			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
		));
	}
	else if ($mode == 'edit')
	{
		$disp_image_data = $image_data;
		if ($submit)
		{
			if (!check_form_key('gallery'))
			{
				trigger_error('FORM_INVALID');
			}

			$image_desc = request_var('message', array(''), true);
			$image_desc = $image_desc[0];
			$image_name = request_var('image_name', array(''), true);
			$image_name = $image_name[0];

			$message_parser				= new parse_message();
			$message_parser->message	= utf8_normalize_nfc($image_desc);
			if ($message_parser->message)
			{
				$message_parser->parse(true, true, true, true, false, true, true, true);
			}

			$sql_ary = array(
				'image_name'				=> $image_name,
				'image_name_clean'			=> utf8_clean_string($image_name),
				'image_desc'				=> $message_parser->message,
				'image_desc_uid'			=> $message_parser->bbcode_uid,
				'image_desc_bitfield'		=> $message_parser->bbcode_bitfield,
				'image_allow_comments'		=> request_var('allow_comments', 0),
			);

			$errors = array();
			if (empty($sql_ary['image_name_clean']))
			{
				$errors[] = $user->lang['MISSING_IMAGE_NAME'];
			}

			if (!$phpbb_ext_gallery->config->get('allow_comments') || !$phpbb_ext_gallery->config->get('comment_user_control'))
			{
				unset($sql_ary['image_allow_comments']);
			}

			$change_image_count = false;
			if ($phpbb_ext_gallery->auth->acl_check('m_edit', $album_id, $album_data['album_user_id']))
			{
				$user_data = phpbb_ext_gallery_core_image::get_new_author_info(request_var('change_author', '', true));
				if ($user_data)
				{
					$sql_ary = array_merge($sql_ary, array(
						'image_user_id'			=> $user_data['user_id'],
						'image_username'		=> $user_data['username'],
						'image_username_clean'	=> utf8_clean_string($user_data['username']),
						'image_user_colour'		=> $user_data['user_colour'],
					));

					if ($image_data['image_status'] != phpbb_ext_gallery_core_image::STATUS_UNAPPROVED)
					{
						$change_image_count = true;
					}
				}
				else if (request_var('change_author', '', true))
				{
					$errors[] = $user->lang['INVALID_USERNAME'];
				}
			}

			$move_to_personal = request_var('move_to_personal', 0);
			if ($move_to_personal)
			{
				$personal_album_id = 0;
				if ($user->data['user_id'] != $image_data['image_user_id'])
				{
					$image_user = new phpbb_gallery_user($db, $image_data['image_user_id']);
					$personal_album_id = $image_user->get_data('personal_album_id');

					// The User has no personal album, moderators can created that without the need of permissions
					if (!$personal_album_id)
					{
						$personal_album_id = phpbb_ext_gallery_core_album::generate_personal_album($image_data['image_username'], $image_data['image_user_id'], $image_data['image_user_colour'], $image_user);
					}
				}
				else
				{
					$personal_album_id = $phpbb_ext_gallery->user->get_data('personal_album_id');
					if (!$personal_album_id && $phpbb_ext_gallery->auth->acl_check('i_upload', phpbb_ext_gallery_core_auth::OWN_ALBUM))
					{
						$personal_album_id = phpbb_ext_gallery_core_album::generate_personal_album($image_data['image_username'], $image_data['image_user_id'], $image_data['image_user_colour'], phpbb_gallery::$user);
					}
				}

				if ($personal_album_id)
				{
					$sql_ary['image_album_id'] = $personal_album_id;
				}
			}

			$additional_sql_data = array();

			$rotate = request_var('rotate', array(0));
			$rotate = (isset($rotate[0])) ? $rotate[0] : 0;
			if ($phpbb_ext_gallery->config->get('allow_rotate') && ($rotate > 0) && (($rotate % 90) == 0))
			{
				$image_tools = new phpbb_ext_gallery_core_file();
				$image_tools->set_image_options($phpbb_ext_gallery->config->get('max_filesize'), $phpbb_ext_gallery->config->get('max_height'), $phpbb_ext_gallery->config->get('max_width'));
				$image_tools->set_image_data($phpbb_ext_gallery->url->path('upload') . $image_data['image_filename']);

				$file_link = $image_tools->image_source;
				$vars = array('additional_sql_data', 'image_data', 'file_link');
				extract($phpbb_dispatcher->trigger_event('gallery.core.posting.edit_before_rotate', compact($vars)));

				// Rotate the image
				$image_tools->rotate_image($rotate, $phpbb_ext_gallery->config->get('allow_resize'));
				if ($image_tools->rotated)
				{
					$image_tools->write_image($image_tools->image_source, $phpbb_ext_gallery->config->get('jpg_quality'), true);
				}

				@unlink($phpbb_ext_gallery->url->path('thumbnail') . $image_data['image_filename']);
				@unlink($phpbb_ext_gallery->url->path('medium') . $image_data['image_filename']);
			}

			$error = implode('<br />', $errors);
			$sql_ary = array_merge($sql_ary, $additional_sql_data);

			if (!$error)
			{
				$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . ' 
					SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE image_id = ' . $image_id;
				$db->sql_query($sql);

				phpbb_ext_gallery_core_album::update_info($album_data['album_id']);
				if ($move_to_personal && $personal_album_id)
				{
					phpbb_ext_gallery_core_album::update_info($personal_album_id);
				}

				if ($change_image_count)
				{
					$new_user = new phpbb_gallery_user($db, $user_data['user_id'], false);
					$new_user->update_images(1);
					$old_user = new phpbb_gallery_user($db, $image_data['image_user_id'], false);
					$old_user->update_images(-1);
				}

				if ($user->data['user_id'] != $image_data['image_user_id'])
				{
					add_log('gallery', $image_data['image_album_id'], $image_id, 'LOG_GALLERY_EDITED', $image_name);
				}

				$message = $user->lang['IMAGES_UPDATED_SUCCESSFULLY'];
				$message .= '<br /><br />' . sprintf($user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
				$message .= '<br /><br />' . sprintf($user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');
				meta_refresh(3, $image_backlink);
				trigger_error($message);
			}
			$disp_image_data = array_merge($disp_image_data, $sql_ary);
		}

		$message_parser				= new parse_message();
		$message_parser->message	= $disp_image_data['image_desc'];
		$message_parser->decode_message($disp_image_data['image_desc_uid']);

		$template->assign_block_vars('image', array(
			'U_IMAGE'		=> phpbb_ext_gallery_core_image::generate_link('thumbnail', 'plugin', $image_id, $image_data['image_name'], $album_id),
			'IMAGE_NAME'	=> $disp_image_data['image_name'],
			'IMAGE_DESC'	=> $message_parser->message,
		));

		$template->assign_vars(array(
			'L_DESCRIPTION_LENGTH'	=> $user->lang('DESCRIPTION_LENGTH', $phpbb_ext_gallery->config->get('description_length')),
			'S_EDIT'			=> true,
			'S_ALBUM_ACTION'	=> $phpbb_ext_gallery->url->append_sid('posting', "mode=edit&amp;album_id=$album_id&amp;image_id=$image_id"),
			'ERROR'				=> (isset($error)) ? $error : '',

			'U_VIEW_IMAGE'		=> $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id"),
			'IMAGE_NAME'		=> $image_data['image_name'],

			'S_CHANGE_AUTHOR'	=> $phpbb_ext_gallery->auth->acl_check('m_edit', $album_id, $album_data['album_user_id']),
			'U_FIND_USERNAME'	=> $phpbb_ext_gallery->url->append_sid('phpbb', 'memberlist', 'mode=searchuser&amp;form=postform&amp;field=change_author&amp;select_single=true'),
			'S_COMMENTS_ENABLED'=> $phpbb_ext_gallery->config->get('allow_comments') && $phpbb_ext_gallery->config->get('comment_user_control'),
			'S_ALLOW_COMMENTS'	=> $image_data['image_allow_comments'],

			'NUM_IMAGES'		=> 1,
			'S_ALLOW_ROTATE'	=> ($phpbb_ext_gallery->config->get('allow_rotate') && function_exists('imagerotate')),
			'S_MOVE_PERSONAL'	=> (($phpbb_ext_gallery->auth->acl_check('i_upload', phpbb_ext_gallery_core_auth::OWN_ALBUM) || $phpbb_ext_gallery->user->get_data('personal_album_id')) || ($user->data['user_id'] != $image_data['image_user_id'])) ? true : false,
			'S_MOVE_MODERATOR'	=> ($user->data['user_id'] != $image_data['image_user_id']) ? true : false,
		));
	}
}

$mode = (strpos($mode, '_')) ? substr($mode, 0, strpos($mode, '_')) : $mode;
page_header($user->lang[strtoupper($mode) . '_IMAGE'], false);

$template->set_filenames(array(
	'body' => 'gallery/posting_body.html',
));

page_footer();

?>