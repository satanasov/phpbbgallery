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
$phpbb_ext_gallery->setup();
$phpbb_ext_gallery->url->_include('functions_phpbb', 'ext');
$phpbb_ext_gallery->url->_include('functions_display', 'phpbb');

$user->add_lang_ext('gallery/core', 'gallery_mcp');

$mode = request_var('mode', 'album');
$action = request_var('action', '');
$option_id = request_var('option_id', 0);
$image_id = request_var('image_id', 0);
$album_id = request_var('album_id', 0);

if ((request_var('quickmod', 0) == 1) && ($action == 'report_details'))
{
	$mode = 'report_details';
	$option_id = (int) $image_data['image_reported'];
}
else if ((request_var('quickmod', 0) == 1) && ($action == 'image_edit'))
{
	$phpbb_ext_gallery->url->redirect('posting', "mode=edit&amp;album_id=$album_id&amp;image_id=$image_id");
}

if ($mode == 'whois' && $auth->acl_get('a_') && request_var('ip', ''))
{
	$phpbb_ext_gallery->url->_include(array('functions_user'), 'phpbb');

	$template->assign_var('WHOIS', user_ipwhois(request_var('ip', '')));

	page_header($user->lang['WHO_IS_ONLINE']);

	$template->set_filenames(array(
		'body' => 'viewonline_whois.html')
	);

	page_footer();
}

//Basic-Information && Permissions
if ($image_id)
{
	$image_data = phpbb_ext_gallery_core_image::get_info($image_id);
	$album_id = $image_data['image_album_id'];
	$user_id = $image_data['image_user_id'];
}
if ($album_id)
{
	$album_data = phpbb_ext_gallery_core_album::get_info($album_id);
}

// Some other variables
$submit = (isset($_POST['submit'])) ? true : false;
$redirect = request_var('redirect', $mode);
$moving_target = request_var('moving_target', 0);
$image_id = ($image_id && !$option_id) ? $image_id : $option_id;
$image_id_ary = ($image_id) ? array($image_id) : request_var('image_id_ary', array(0));

/**
* Check for all the requested permissions
*/
$access_denied = false;
switch ($mode)
{
	case 'report_open':
	case 'report_closed':
	case 'report_details':
		if ($album_id)
		{
			$access_denied = (!$phpbb_ext_gallery->auth->acl_check('m_report', $album_id, $album_data['album_user_id'])) ? true : false;
		}
		else
		{
			//@todo: Create a methos that is specified for empty()
			$acl_album_ids = $phpbb_ext_gallery->auth->acl_album_ids('m_report');
			$access_denied = (empty($acl_album_ids)) ? true : false;
		}
	break;
	case 'queue_unapproved':
	case 'queue_approved':
	case 'queue_locked':
	case 'queue_details':
		if ($album_id)
		{
			$access_denied = (!$phpbb_ext_gallery->auth->acl_check('m_status', $album_id, $album_data['album_user_id'])) ? true : false;
		}
		else
		{
			$access_denied = (!sizeof($phpbb_ext_gallery->auth->acl_album_ids('m_status'))) ? true : false;
		}
	break;
	case 'overview':
		$access_denied = (!$phpbb_ext_gallery->auth->acl_check_global('m_')) ? true : false;
	break;
}
switch ($action)
{
	case 'images_move':
		$access_denied = (!$phpbb_ext_gallery->auth->acl_check('m_move', $album_id, $album_data['album_user_id']) || ($moving_target && !$phpbb_ext_gallery->auth->acl_check('i_upload', $moving_target))) ? true : false;
	break;
	case 'images_unapprove':
	case 'images_approve':
	case 'images_lock':
		$access_denied = (!$phpbb_ext_gallery->auth->acl_check('m_status', $album_id, $album_data['album_user_id'])) ? true : false;
	break;
	case 'images_delete':
		$access_denied = (!$phpbb_ext_gallery->auth->acl_check('m_delete', $album_id, $album_data['album_user_id'])) ? true : false;
	break;
	case 'reports_close':
	case 'reports_open':
	case 'reports_delete':
		$access_denied = (!$phpbb_ext_gallery->auth->acl_check('m_report', $album_id, $album_data['album_user_id'])) ? true : false;
	break;
}

if ($access_denied || (($album_id && !$phpbb_ext_gallery->auth->acl_check('m_', $album_id, $album_data['album_user_id'])) || (!$album_id && !sizeof($phpbb_ext_gallery->auth->acl_album_ids('m_')))))
{
	if (!$album_id)
	{
		meta_refresh(5, $phpbb_ext_gallery->url->append_sid('index'));
	}
	else
	{
		meta_refresh(5, $phpbb_ext_gallery->url->append_sid('album', "album_id=$album_id"));
	}
	trigger_error('NOT_AUTHORISED');
}

if ($mode == 'overview')
{
	$page_title = $user->lang['GALLERY_MCP_OVERVIEW'];

	$template->assign_vars(array(
		'S_MODE_OVERVIEW'	=> true,
		'SUBSECTION'		=> $user->lang['GALLERY_MCP_OVERVIEW'],

		'REPORTED_IMG'				=> $user->img('icon_topic_reported', 'IMAGE_REPORTED'),
		'UNAPPROVED_IMG'			=> $user->img('icon_topic_unapproved', 'IMAGE_UNAPPROVED'),
		'DISP_FAKE_THUMB'			=> $phpbb_ext_gallery->config->get('mini_thumbnail_disp'),
		'FAKE_THUMB_SIZE'			=> $phpbb_ext_gallery->config->get('mini_thumbnail_size'),

		'NO_REPORTED_IMAGE'			=> $user->lang('WAITING_REPORTED_IMAGE', 0),
		'NO_UNAPPROVED_IMAGE'		=> $user->lang('WAITING_UNAPPROVED_IMAGE', 0),
	));

	$sql = 'SELECT image_time, image_name, image_id, image_album_id, image_user_id, image_username, image_user_colour
		FROM ' . GALLERY_IMAGES_TABLE . '
		WHERE image_status = ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . '
			AND ' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('m_status', 'array'), false, true) . '
		ORDER BY image_time DESC';
	$result = $db->sql_query_limit($sql, 5);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('queue_row', array(
			'THUMBNAIL'			=> phpbb_ext_gallery_core_image::generate_link('fake_thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $row['image_id'], $row['image_name'], $row['image_album_id']),
			'UPLOADER'			=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
			'IMAGE_TIME'		=> $user->format_date($row['image_time']),
			'IMAGE_NAME'		=> $row['image_name'],
			'IMAGE_ID'			=> $row['image_id'],
			'U_IMAGE'			=> $phpbb_ext_gallery->url->append_sid('image', 'album_id=' . $row['image_album_id'] . '&amp;image_id=' . $row['image_id']),
			'U_IMAGE_PAGE'		=> $phpbb_ext_gallery->url->append_sid('mcp', 'mode=queue_details&amp;album_id=' . $row['image_album_id'] . '&amp;option_id=' . $row['image_id']),
		));
	}
	$db->sql_freeresult($result);

	$sql_array = array(
		'SELECT'		=> 'r.*, u.username reporter_name, u.user_colour reporter_colour, m.username mod_username, m.user_colour mod_user_colour, i.*',
		'FROM'			=> array(GALLERY_REPORTS_TABLE => 'r'),

		'LEFT_JOIN'		=> array(
			array(
				'FROM'		=> array(USERS_TABLE => 'u'),
				'ON'		=> 'r.reporter_id = u.user_id',
			),
			array(
				'FROM'		=> array(USERS_TABLE => 'm'),
				'ON'		=> 'r.report_manager = m.user_id',
			),
			array(
				'FROM'		=> array(GALLERY_IMAGES_TABLE => 'i'),
				'ON'		=> 'r.report_image_id = i.image_id',
			),
		),

		'WHERE'			=> 'r.report_status = ' . phpbb_ext_gallery_core_report::OPEN . ' AND ' . $db->sql_in_set('r.report_album_id', $phpbb_ext_gallery->auth->acl_album_ids('m_report', 'array'), false, true),
		'ORDER_BY'		=> 'report_time DESC',
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query_limit($sql, 5);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('report_row', array(
			'THUMBNAIL'			=> phpbb_ext_gallery_core_image::generate_link('fake_thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $row['image_id'], $row['image_name'], $row['image_album_id']),
			'REPORTER'			=> get_username_string('full', $row['reporter_id'], $row['reporter_name'], $row['reporter_colour']),
			'UPLOADER'			=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
			'REPORT_ID'			=> $row['report_id'],
			'REPORT_MOD'		=> ($row['report_manager']) ? get_username_string('full', $row['report_manager'], $row['mod_username'], $row['mod_user_colour']) : '',
			'REPORT_TIME'		=> $user->format_date($row['report_time']),
			'IMAGE_TIME'		=> $user->format_date($row['image_time']),
			'IMAGE_NAME'		=> $row['image_name'],
			'U_IMAGE'			=> $phpbb_ext_gallery->url->append_sid('image', 'album_id=' . $row['image_album_id'] . '&amp;image_id=' . $row['image_id']),
			'U_IMAGE_PAGE'		=> $phpbb_ext_gallery->url->append_sid('mcp', 'mode=report_details&amp;album_id=' . $row['image_album_id'] . '&amp;option_id=' . $row['report_id']),
		));
	}
	$db->sql_freeresult($result);

}
else
{
	// Build Navigation
	$page_title = phpbb_ext_gallery_core_mcp::build_navigation($album_id, $mode, $option_id);
}

if (!$album_id)
{
	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME'	=> $user->lang['MCP'],
		'U_VIEW_FORUM'	=> $phpbb_ext_gallery->url->append_sid('mcp', 'mode=overview'),
	));

	page_header($user->lang['GALLERY'] . ' &bull; ' . $user->lang['MCP'] . ' &bull; ' . $page_title, false);

	$template->set_filenames(array(
		'body' => 'gallery/mcp_body.html')
	);

	page_footer();
}

phpbb_ext_gallery_core_album_display::generate_nav($album_data);
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['MCP'],
	'U_VIEW_FORUM'	=> $phpbb_ext_gallery->url->append_sid('mcp', 'album_id=' . $album_data['album_id']),
));

$template->assign_vars(array(
	'S_ALLOWED_MOVE'	=> ($phpbb_ext_gallery->auth->acl_check('m_move', $album_id, $album_data['album_user_id'])) ? true : false,
	'S_ALLOWED_STATUS'	=> ($phpbb_ext_gallery->auth->acl_check('m_status', $album_id, $album_data['album_user_id'])) ? true : false,
	'S_ALLOWED_DELETE'	=> ($phpbb_ext_gallery->auth->acl_check('m_delete', $album_id, $album_data['album_user_id'])) ? true : false,
	'S_ALLOWED_REPORT'	=> ($phpbb_ext_gallery->auth->acl_check('m_report', $album_id, $album_data['album_user_id'])) ? true : false,
	'EDIT_IMG'		=> $user->img('icon_post_edit', 'EDIT_IMAGE'),
	'DELETE_IMG'	=> $user->img('icon_post_delete', 'DELETE_IMAGE'),
	'ALBUM_NAME'	=> $album_data['album_name'],
	'ALBUM_IMAGES'	=> $user->lang('IMAGE_COUNT', $album_data['album_images']) . ' ' . (($album_data['album_images'] == 1) ? $user->lang['IMAGE'] : $user->lang['IMAGES']),
	'U_VIEW_ALBUM'	=> $phpbb_ext_gallery->url->append_sid('album', 'album_id=' . $album_id),
	'U_MOD_ALBUM'	=> $phpbb_ext_gallery->url->append_sid('mcp', 'mode=album&amp;album_id=' . $album_id),
	'U_MCP_OVERVIEW'	=> $phpbb_ext_gallery->url->append_sid('mcp', 'mode=overview'),
));

if ($action && $image_id_ary)
{
	$s_hidden_fields = build_hidden_fields(array(
		'mode'				=> $mode,
		'album_id'			=> $album_id,
		'image_id_ary'		=> $image_id_ary,
		'action'			=> $action,
		'redirect'			=> $redirect,
	));
	$multiple = '';
	if (isset($image_id_ary[1]))
	{
		// We add an S to the lang string (IMAGE), when we have more than one image, so we get IMAGES
		$multiple = 'S';
	}
	switch ($action)
	{
		case 'images_move':
			if ($moving_target)
			{
				$target_data = phpbb_ext_gallery_core_album::get_info($moving_target);

				if ($target_data['contest_id'] && (time() < ($target_data['contest_start'] + $target_data['contest_end'])))
				{
					$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
						SET image_album_id = ' . $moving_target . ',
							image_contest = ' . phpbb_ext_gallery_core_image::IN_CONTEST . '
						WHERE ' . $db->sql_in_set('image_id', $image_id_ary);
					$db->sql_query($sql);
				}
				else
				{
					$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
						SET image_album_id = ' . $moving_target . ',
							image_contest = ' . phpbb_ext_gallery_core_image::NO_CONTEST . '
						WHERE ' . $db->sql_in_set('image_id', $image_id_ary);
					$db->sql_query($sql);
				}
				phpbb_ext_gallery_core_report::move_images($image_id_ary, $moving_target);

				foreach ($image_id_ary as $image)
				{
					add_log('gallery', $moving_target, $image, 'LOG_GALLERY_MOVED', $album_data['album_name'], $target_data['album_name']);
				}

				$success = true;
			}
			else
			{
				$category_select = phpbb_ext_gallery_core_album::get_albumbox(false, 'moving_target', $album_id, 'i_upload', $album_id);
				$template->assign_vars(array(
					'S_MOVING_IMAGES'	=> true,
					'S_ALBUM_SELECT'	=> $category_select,
					'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
				));
			}

		break;
		case 'images_unapprove':
			if (confirm_box(true))
			{
				phpbb_ext_gallery_core_image::handle_counter($image_id_ary, false);

				$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
					SET image_status = ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND ' . $db->sql_in_set('image_id', $image_id_ary);
				$db->sql_query($sql);

				$sql = 'SELECT image_id, image_name
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND ' . $db->sql_in_set('image_id', $image_id_ary);
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_UNAPPROVED', $row['image_name']);
				}
				$db->sql_freeresult($result);

				$success = true;
			}
			else
			{
				confirm_box(false, 'QUEUE' . $multiple . '_A_UNAPPROVE2', $s_hidden_fields);
			}
		break;
		case 'images_approve':
			if (confirm_box(true))
			{
				phpbb_ext_gallery_core_image::handle_counter($image_id_ary, true, true);

				$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
					SET image_status = ' . phpbb_ext_gallery_core_image::STATUS_APPROVED . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND ' . $db->sql_in_set('image_id', $image_id_ary);
				$db->sql_query($sql);

				$image_names = array();
				$sql = 'SELECT image_id, image_name
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND ' . $db->sql_in_set('image_id', $image_id_ary);
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_APPROVED', $row['image_name']);
				}
				$db->sql_freeresult($result);

				$success = true;
			}
			else
			{
				confirm_box(false, 'QUEUE' . $multiple . '_A_APPROVE2', $s_hidden_fields);
			}
		break;
		case 'images_lock':
			if (confirm_box(true))
			{
				phpbb_ext_gallery_core_image::handle_counter($image_id_ary, false);

				$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
					SET image_status = ' . phpbb_ext_gallery_core_image::STATUS_LOCKED . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND ' . $db->sql_in_set('image_id', $image_id_ary);
				$db->sql_query($sql);

				$sql = 'SELECT image_id, image_name
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND ' . $db->sql_in_set('image_id', $image_id_ary);
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_LOCKED', $row['image_name']);
				}
				$db->sql_freeresult($result);

				$success = true;
			}
			else
			{
				confirm_box(false, 'QUEUE' . $multiple . '_A_LOCK2', $s_hidden_fields);
			}
		break;
		case 'images_delete':
			if (confirm_box(true))
			{
				phpbb_ext_gallery_core_image::handle_counter($image_id_ary, false);

				// Delete the files
				$sql = 'SELECT image_id, image_name, image_filename
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE ' . $db->sql_in_set('image_id', $image_id_ary);
				$result = $db->sql_query($sql);

				$filenames = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$filenames[(int) $row['image_id']] = $row['image_filename'];
					add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_DELETED', $row['image_name']);
				}
				$db->sql_freeresult($result);

				phpbb_ext_gallery_core_image::delete_images($image_id_ary, $filenames);

				$success = true;
			}
			else
			{
				confirm_box(false, 'QUEUE' . $multiple . '_A_DELETE2', $s_hidden_fields);
			}
		break;
		case 'reports_close':
			if (confirm_box(true))
			{
				$sql = 'SELECT image_id, image_name
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE ' . $db->sql_in_set('image_reported', $image_id_ary);
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_REPORT_CLOSED', $row['image_name']);
				}
				$db->sql_freeresult($result);

				phpbb_ext_gallery_core_report::change_status(phpbb_ext_gallery_core_report::LOCKED, $image_id_ary);

				$success = true;
			}
			else
			{
				confirm_box(false, 'REPORT' . $multiple . '_A_CLOSE2', $s_hidden_fields);
			}
		break;
		case 'reports_open':
			if (confirm_box(true))
			{
				phpbb_ext_gallery_core_report::change_status(phpbb_ext_gallery_core_report::OPEN, $image_id_ary);

				$sql = 'SELECT image_id, image_name
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE ' . $db->sql_in_set('image_reported', $image_id_ary);
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_REPORT_OPENED', $row['image_name']);
				}
				$db->sql_freeresult($result);

				$success = true;
			}
			else
			{
				confirm_box(false, 'REPORT' . $multiple . '_A_OPEN2', $s_hidden_fields);
			}
		break;
		case 'reports_delete':
			if (confirm_box(true))
			{
				$sql = 'SELECT image_id, image_name
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE ' . $db->sql_in_set('image_reported', $image_id_ary);
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					add_log('gallery', $album_id, $row['image_id'], 'LOG_GALLERY_REPORT_DELETED', $row['image_name']);
				}
				$db->sql_freeresult($result);

				phpbb_ext_gallery_core_report::delete($image_id_ary);
				$success = true;
			}
			else
			{
				confirm_box(false, 'REPORT' . $multiple . '_A_DELETE2', $s_hidden_fields);
			}
		break;
	}

	if (isset($success))
	{
		phpbb_ext_gallery_core_album::update_info($album_id);
		if ($moving_target)
		{
			phpbb_ext_gallery_core_album::update_info($moving_target);
		}
		redirect(($redirect == 'redirect') ? $phpbb_ext_gallery->url->append_sid('album', "album_id=$album_id") : $phpbb_ext_gallery->url->append_sid('mcp', "mode=$mode&amp;album_id=$album_id"));
	}
}// end if ($action && $image_id_ary)

switch ($mode)
{
	case 'album':
		phpbb_ext_gallery_core_mcp::album($mode, $album_id, $album_data);
	break;

	case 'report_open':
	case 'report_closed':
		phpbb_ext_gallery_core_mcp::report($mode, $album_id, $album_data);
	break;

	case 'queue_unapproved':
	case 'queue_approved':
	case 'queue_locked':
		phpbb_ext_gallery_core_mcp::queue($mode, $album_id, $album_data);
	break;

	case 'report_details':
	case 'queue_details':
		phpbb_ext_gallery_core_mcp::details($mode, $option_id, $album_id, $album_data);
	break;
}

page_header($user->lang['GALLERY'] . ' &bull; ' . $user->lang['MCP'] . ' &bull; ' . $page_title, false);

$template->set_filenames(array(
	'body' => 'gallery/mcp_body.html')
);

page_footer();
