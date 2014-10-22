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
$comment_id = request_var('comment_id', 0);
$error = $message = '';

// Check for permissions cheaters!
if ($comment_id)
{
	$sql = 'SELECT *
		FROM ' . GALLERY_COMMENTS_TABLE . '
		WHERE comment_id = ' . $comment_id;
	$result = $db->sql_query($sql);
	$comment_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	$image_id = (int) $comment_data['comment_image_id'];
}

if ($image_id)
{
	$image_data = phpbb_ext_gallery_core_image::get_info($image_id);
	$album_id = (int) $image_data['image_album_id'];
}

$album_data = phpbb_ext_gallery_core_album::get_info($album_id);

phpbb_ext_gallery_core_album_display::generate_nav($album_data);

$image_backlink = $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id");
$album_backlink = $phpbb_ext_gallery->url->append_sid('album', "album_id=$album_id");
$image_loginlink = $phpbb_ext_gallery->url->append_sid('relative', 'image_page', "album_id=$album_id&amp;image_id=$image_id");

// Send some cheaters back
if ($user->data['is_bot'])
{
	redirect($image_backlink);
}

if ($album_data['album_type'] == phpbb_ext_gallery_core_album::TYPE_CAT)
{
	// If we get here, the database is corrupted,
	// but at least we dont let them comment any more.
	meta_refresh(3, $album_backlink);
	trigger_error('ALBUM_IS_CATEGORY');
}

if (!in_array($mode, array('rate', 'add', 'edit', 'delete')))
{
	phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
}

if (($mode != 'rate') && !phpbb_ext_gallery_core_comment::is_able($album_data, $image_data))
{
	// The user is unable to comment.
	phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
}

$rating = new phpbb_ext_gallery_core_rating($image_id, $image_data, $album_data);
if (!($phpbb_ext_gallery->config->get('allow_rates') && $rating->is_able()) && ($mode == 'rate'))
{
	// The user is unable to rate.
	phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
}

switch ($mode)
{
	case 'add':
		if (!$phpbb_ext_gallery->auth->acl_check('c_post', $album_id, $album_data['album_user_id']))
		{
			phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
		}
	break;

	case 'edit':
		if (!$phpbb_ext_gallery->auth->acl_check('c_edit', $album_id, $album_data['album_user_id']))
		{
			if (!$phpbb_ext_gallery->auth->acl_check('m_comments', $album_id, $album_data['album_user_id']))
			{
				phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
			}
		}
		else if (($comment_data['comment_user_id'] != $user->data['user_id']) && !$phpbb_ext_gallery->auth->acl_check('m_comments', $album_id, $album_data['album_user_id']))
		{
			phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
		}
	break;

	case 'delete':
		if (!$phpbb_ext_gallery->auth->acl_check('c_delete', $album_id, $album_data['album_user_id']))
		{
			if (!$phpbb_ext_gallery->auth->acl_check('m_comments', $album_id, $album_data['album_user_id']))
			{
				phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
			}
		}
		else if (($comment_data['comment_user_id'] != $user->data['user_id']) && !$phpbb_ext_gallery->auth->acl_check('m_comments', $album_id, $album_data['album_user_id']))
		{
			phpbb_ext_gallery_core_misc::not_authorised($image_backlink, $image_loginlink);
		}
	break;
}


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

	'S_BBCODE_ALLOWED'			=> $bbcode_status,
	'S_SMILIES_ALLOWED'			=> $smilies_status,
	'S_LINKS_ALLOWED'			=> $url_status,
	'S_BBCODE_IMG'			=> $img_status,
	'S_BBCODE_URL'			=> $url_status,
	'S_BBCODE_FLASH'		=> $flash_status,
	'S_BBCODE_QUOTE'		=> $quote_status,
));

// Build custom bbcodes array
display_custom_bbcodes();

// Build smilies array
generate_smilies('inline', 0);

/**
* Rating-System: now you can comment and rate in one form
*/
$s_user_rated = false;
if ($phpbb_ext_gallery->config->get('allow_rates') && ($mode != 'edit'))
{
	$user_rating = $rating->get_user_rating($user->data['user_id']);

	// Check: User didn't rate yet, has permissions, it's not the users own image and the user is logged in
	if (!$user_rating && $rating->is_allowed())
	{
		$rating->display_box();

		// User just rated the image, so we store it
		$rate_point = request_var('rating', 0);
		if ($rating->rating_enabled && $rate_point > 0)
		{
			$rating->submit_rating();
			$s_user_rated = true;

			$message .= $user->lang['RATING_SUCCESSFUL'] . '<br />';
		}
		$template->assign_vars(array(
			'S_ALLOWED_TO_RATE'			=> $rating->is_allowed(),
		));
	}
	if ($mode == 'rate')
	{
		$s_album_action = '';
	}
}

if ($mode == 'add')
{
	if (phpbb_ext_gallery_core_misc::display_captcha('comment'))
	{
		$phpbb_ext_gallery->url->_include('captcha/captcha_factory', 'phpbb');
		$captcha =& phpbb_captcha_factory::get_instance($config['captcha_plugin']);
		$captcha->init(CONFIRM_POST);
	}

	$s_captcha_hidden_fields = '';
	$comment_username_req = ($user->data['user_id'] == ANONYMOUS);

	if ($submit)
	{
		if (!check_form_key('gallery'))
		{
			trigger_error('FORM_INVALID');
		}
		if (phpbb_ext_gallery_core_misc::display_captcha('comment'))
		{
			$captcha_error = $captcha->validate();
			if ($captcha_error)
			{
				$error .= (($error) ? '<br />' : '') . $captcha_error;
			}
		}

		$comment_plain = request_var('message', '', true);
		$comment_username = request_var('username', '', true);

		if ($comment_username_req)
		{
			if ($comment_username == '')
			{
				$error .= (($error) ? '<br />' : '') . $user->lang['MISSING_USERNAME'];
			}
			if ($result = validate_username($comment_username))
			{
				$user->add_lang('ucp');
				$error .= (($error) ? '<br />' : '') . $user->lang[$result . '_USERNAME'];
				$submit = false;
			}
		}
		if (($comment_plain == '') && !$s_user_rated)
		{
			$error .= (($error) ? '<br />' : '') . $user->lang['MISSING_COMMENT'];
		}
		if (utf8_strlen($comment_plain) > $phpbb_ext_gallery->config->get('comment_length'))
		{
			$error .= (($error) ? '<br />' : '') . $user->lang['COMMENT_TOO_LONG'];
		}

		$message_parser				= new parse_message();
		$message_parser->message	= utf8_normalize_nfc($comment_plain);
		if ($message_parser->message)
		{
			$message_parser->parse(true, true, true, true, false, true, true, true);
		}
		$sql_ary = array(
			'comment_image_id'		=> $image_id,
			'comment'				=> $message_parser->message,
			'comment_uid'			=> $message_parser->bbcode_uid,
			'comment_bitfield'		=> $message_parser->bbcode_bitfield,
			'comment_signature'		=> ($auth->acl_get('u_sig') && isset($_POST['attach_sig'])),
		);
		if ((!$error) && ($sql_ary['comment'] != ''))
		{
			if (phpbb_ext_gallery_core_misc::display_captcha('comment'))
			{
				$captcha->reset();
			}

			phpbb_ext_gallery_core_comment::add($sql_ary, $comment_username);
			if ($phpbb_ext_gallery->user->get_data('watch_com') && !$image_data['watch_id'])
			{
				phpbb_ext_gallery_core_notification::add($image_id);
			}

			phpbb_ext_gallery_core_notification::send_notification('image', $image_id, $image_data['image_name']);
			$message .= $user->lang['COMMENT_STORED'] . '<br />';
		}
		else if (phpbb_ext_gallery_core_misc::display_captcha('comment'))
		{
			$s_captcha_hidden_fields = ($captcha->is_solved()) ? build_hidden_fields($captcha->get_hidden_fields()) : '';
		}
		$sig_checked = ($auth->acl_get('u_sig') && isset($_POST['attach_sig']));
	}
	else
	{
		if ($comment_id)
		{
			$comment_ary = generate_text_for_edit($comment_data['comment'], $comment_data['comment_uid'], $comment_data['comment_bitfield'], 7);
			$comment_plain = '[quote="' . $comment_data['comment_username'] . '"]' . $comment_ary['text'] . '[/quote]';
		}
		$sig_checked = $user->optionget('attachsig');
	}

	if (phpbb_ext_gallery_core_misc::display_captcha('comment'))
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
else if ($mode == 'edit')
{
	$comment_username_req = ($comment_data['comment_user_id'] == ANONYMOUS) ? true : false;

	if ($submit)
	{
		if (!check_form_key('gallery'))
		{
			trigger_error('FORM_INVALID');
		}

		$sql_ary = array();
		$comment_plain = request_var('message', '', true);

		if ($comment_username_req)
		{
			$comment_username = request_var('username', '');
			if ($comment_username == '')
			{
				$error .= (($error) ? '<br />' : '') . $user->lang['MISSING_USERNAME'];
			}

			if (validate_username($comment_username))
			{
				$error .= (($error) ? '<br />' : '') . $user->lang['INVALID_USERNAME'];
				$comment_username = '';
			}

			$sql_ary = array(
				'comment_username'	=> $comment_username,
			);
		}

		if ($comment_plain == '')
		{
			$error .= (($error) ? '<br />' : '') . $user->lang['MISSING_COMMENT'];
		}
		if (utf8_strlen($comment_plain) > $phpbb_ext_gallery->config->get('comment_length'))
		{
			$error .= (($error) ? '<br />' : '') . $user->lang['COMMENT_TOO_LONG'];
		}

		$message_parser				= new parse_message();
		$message_parser->message	= utf8_normalize_nfc($comment_plain);
		if ($message_parser->message)
		{
			$message_parser->parse(true, true, true, true, false, true, true, true);
		}

		$sql_ary = array_merge($sql_ary, array(
			'comment'				=> $message_parser->message,
			'comment_uid'			=> $message_parser->bbcode_uid,
			'comment_bitfield'		=> $message_parser->bbcode_bitfield,
			'comment_edit_count'	=> $comment_data['comment_edit_count'] + 1,
			'comment_signature'		=> ($auth->acl_get('u_sig') && isset($_POST['attach_sig'])),
		));

		if (!$error)
		{
			phpbb_ext_gallery_core_comment::edit($comment_id, $sql_ary);
			$message .= $user->lang['COMMENT_STORED'] . '<br />';
			if ($user->data['user_id'] != $comment_data['comment_user_id'])
			{
				add_log('gallery', $image_data['image_album_id'], $image_data['image_id'], 'LOG_GALLERY_COMMENT_EDITED', $image_data['image_name']);
			}
		}
	}
	else
	{
		$sig_checked = (bool) $comment_data['comment_signature'];

		$comment_ary = generate_text_for_edit($comment_data['comment'], $comment_data['comment_uid'], $comment_data['comment_bitfield'], 7);
		$comment_plain = $comment_ary['text'];
		$comment_username = $comment_data['comment_username'];
	}
}
else if ($mode == 'delete')
{
	$s_hidden_fields = build_hidden_fields(array(
		'album_id'		=> $album_id,
		'image_id'		=> $image_id,
		'comment_id'	=> $comment_id,
		'mode'			=> 'delete',
	));

	if (confirm_box(true))
	{
		phpbb_ext_gallery_core_comment::delete_comments($comment_id);
		if ($user->data['user_id'] != $comment_data['comment_user_id'])
		{
			add_log('gallery', $image_data['image_album_id'], $image_data['image_id'], 'LOG_GALLERY_COMMENT_DELETED', $image_data['image_name']);
		}

		$message = $user->lang['DELETED_COMMENT'] . '<br />';
		$submit = true;
	}
	else
	{
		if (isset($_POST['cancel']))
		{
			$message = $user->lang['DELETED_COMMENT_NOT'] . '<br />';
			$submit = true;
		}
		else
		{
			confirm_box(false, 'DELETE_COMMENT2', $s_hidden_fields);
		}
	}
}

$template->assign_vars(array(
	'ERROR'					=> $error,
	'MESSAGE'				=> (isset($comment_plain)) ? $comment_plain : '',
	'USERNAME'				=> (isset($comment_username)) ? $comment_username : '',
	'REQ_USERNAME'			=> (!empty($comment_username_req)) ? true : false,
	'L_COMMENT_LENGTH'		=> sprintf($user->lang['COMMENT_LENGTH'], $phpbb_ext_gallery->config->get('comment_length')),

	'IMAGE_RSZ_WIDTH'		=> $phpbb_ext_gallery->config->get('medium_width'),
	'IMAGE_RSZ_HEIGHT'		=> $phpbb_ext_gallery->config->get('medium_height'),
	'U_IMAGE'				=> $phpbb_ext_gallery->url->append_sid('image', "album_id=$album_id&amp;image_id=$image_id"),
	'U_VIEW_IMAGE'			=> $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id"),
	'IMAGE_NAME'			=> $image_data['image_name'],

	'S_SIGNATURE_CHECKED'	=> (isset($sig_checked) && $sig_checked) ? ' checked="checked"' : '',
	'S_ALBUM_ACTION'		=> $phpbb_ext_gallery->url->append_sid('comment', "mode=$mode&amp;album_id=$album_id&amp;image_id=$image_id" . (($comment_id) ? "&amp;comment_id=$comment_id" : '')),
));

if ($submit && !$error)
{
	$message .= '<br />' . sprintf($user->lang['CLICK_RETURN_IMAGE'], '<a href="' . $image_backlink . '">', '</a>');
	$message .= '<br />' . sprintf($user->lang['CLICK_RETURN_ALBUM'], '<a href="' . $album_backlink . '">', '</a>');

	meta_refresh(3, $image_backlink);
	trigger_error($message);
}

page_header((($mode == 'add') ? $user->lang['POST_COMMENT'] : $user->lang['EDIT_COMMENT']), false);

$template->set_filenames(array(
	'body' => 'gallery/comment_body.html',
));

page_footer();

?>