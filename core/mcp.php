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

if (!defined('IN_PHPBB'))
{
	exit;
}

class phpbb_ext_gallery_core_mcp
{
	static protected $allowed_sort_params = array('image_time', 'image_name_clean', 'image_username_clean', 'image_view_count', 'image_rate_avg', 'image_comments', 'image_last_comment');
	static protected $allowed_sort_params_report = array('reporter_name', 'mod_username');

	static public function build_navigation($album_id, $mode, $option_id = false)
	{
		global $user, $template, $phpbb_ext_gallery;

		$mode_s = $mode;
		$nav_tabs = array(
			'album'			=> array('name' => 'GALLERY_MCP_MAIN',		'mode' => 'album',				'mode_s' => 'album'),
			'report'		=> array('name' => 'GALLERY_MCP_REPORTED',	'mode' => 'report_open',		'mode_s' => 'report'),
			'queue'			=> array('name' => 'GALLERY_MCP_QUEUE',		'mode' => 'queue_unapproved',	'mode_s' => 'queue'),
		);
		$nav_subsections = array(
			'album'		=> array(
				//array('name' => 'GALLERY_MCP_OVERVIEW', 'mode' => 'overview'),
				array('name' => 'GALLERY_MCP_VIEWALBUM', 'mode' => 'album'),
			),
			'report'		=> array(
				array('name' => 'GALLERY_MCP_REPO_OPEN', 'mode' => 'report_open'),
				array('name' => 'GALLERY_MCP_REPO_DONE', 'mode' => 'report_closed'),
			),
			'queue'		=> array(
				array('name' => 'GALLERY_MCP_UNAPPROVED', 'mode' => 'queue_unapproved'),
				array('name' => 'GALLERY_MCP_APPROVED', 'mode' => 'queue_approved'),
				array('name' => 'GALLERY_MCP_LOCKED', 'mode' => 'queue_locked'),
			),
		);
		if ($mode == 'queue_details')
		{
			$nav_subsections['queue'][] = array('name' => 'GALLERY_MCP_QUEUE_DETAIL', 'mode' => 'queue_details');
		}
		if ($mode == 'report_details')
		{
			$nav_subsections['report'][] = array('name' => 'GALLERY_MCP_REPO_DETAIL', 'mode' => 'report_details');
		}
		// Hide tabs if permissions are denied
		if (!$phpbb_ext_gallery->auth->acl_check('m_report', $album_id))
		{
			unset($nav_tabs['report']);
		}
		if (!$phpbb_ext_gallery->auth->acl_check('m_status', $album_id))
		{
			unset($nav_tabs['queue']);
		}
		foreach ($nav_tabs as $navtab)
		{
			$template->assign_block_vars('tabs', array(
				'TAB_ACTIVE'	=> (strrpos(substr($mode, 0, 5), substr($navtab['mode_s'], 0, 5)) !== false) ? true : false,
				'TAB_NAME'		=> $user->lang[$navtab['name']],
				'U_TAB'			=> $phpbb_ext_gallery->url->append_sid('mcp', 'mode=' .  $navtab['mode'] . '&amp;album_id=' . $album_id),
			));

			if (strrpos(substr($mode, 0, 5), substr($navtab['mode_s'], 0, 5)) !== false)
			{
				$mode_s = $navtab['mode_s'];
				foreach ($nav_subsections[$mode_s] as $navsubsection)
				{
					$template->assign_block_vars('tabs.modes', array(
						'MODE_ACTIVE'		=> ($navsubsection['mode'] == $mode) ? true : false,
						'MODE_NAME'			=> $user->lang[$navsubsection['name']],
						'U_MODE'			=> $phpbb_ext_gallery->url->append_sid('mcp', 'mode=' .  $navsubsection['mode'] . '&amp;album_id=' . $album_id . (($option_id && (($navsubsection['mode'] == 'report_details') || ($navsubsection['mode'] == 'queue_details'))) ? '&amp;option_id=' . $option_id : '')),
					));

					if ($navsubsection['mode'] == $mode)
					{
						$page_title = $user->lang[$navsubsection['name']];
						$template->assign_vars(array(
							'S_' . $navsubsection['name']	=> true,
							'SUBSECTION'					=> $page_title,
						));
					}
				}
			}
		}

		return $page_title;
	}

	static public function album($mode, $album_id, $album_data)
	{
		global $config, $db, $template, $user, $phpbb_ext_gallery;

		$start				= request_var('start', 0);
		$sort_key			= request_var('sk', 'image_time');
		$sort_dir			= (request_var('sd', 'DESC') == 'DESC') ? 'DESC' : 'ASC';
		$images_per_page	= $config['topics_per_page'];
		$count_images		= $album_data['album_images_real'];

		$use_sort_key = $sort_key;
		if (!in_array($use_sort_key, self::$allowed_sort_params))
		{
			if (in_array($use_sort_key . '_clean', self::$allowed_sort_params))
			{
				$use_sort_key .= '_clean';
			}
			else
			{
				$use_sort_key = 'image_time';
			}
		}

		$m_status = ' AND image_status <> ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED;
		if ($phpbb_ext_gallery->auth->acl_check('m_status', $album_id))
		{
			$m_status = '';
		}

		$sql_array = array(
			'SELECT'		=> 'i.*, r.report_status, r.report_id',
			'FROM'			=> array(GALLERY_IMAGES_TABLE => 'i'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array(GALLERY_REPORTS_TABLE => 'r'),
					'ON'		=> 'r.report_image_id = i.image_id',
				),
			),

			'WHERE'			=> 'i.image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
									AND i.image_album_id = ' . $album_id . ' ' . $m_status,
			'ORDER_BY'		=> "i.$use_sort_key $sort_dir",
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);

		$result = $db->sql_query_limit($sql, $images_per_page, $start);
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('image_row', array(
				'THUMBNAIL'			=> phpbb_ext_gallery_core_image::generate_link('fake_thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $row['image_id'], $row['image_name'], $album_id),
				'UPLOADER'			=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
				'IMAGE_TIME'		=> $user->format_date($row['image_time']),
				'IMAGE_NAME'		=> $row['image_name'],
				'COMMENTS'			=> $row['image_comments'],
				'RATING'			=> ($row['image_rate_avg'] / 100),
				'STATUS'			=> $user->lang['QUEUE_STATUS_' . $row['image_status']],
				'IMAGE_ID'			=> $row['image_id'],
				'S_REPORTED'		=> (isset($row['report_status']) && ($row['report_status'] == phpbb_ext_gallery_core_report::OPEN)) ? true : false,
				'S_UNAPPROVED'		=> ($row['image_status'] == phpbb_ext_gallery_core_image::STATUS_UNAPPROVED) ? true : false,
				'U_IMAGE'			=> $phpbb_ext_gallery->url->append_sid('image', "album_id=$album_id&amp;image_id=" . $row['image_id']),
				'U_IMAGE_PAGE'		=> $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=" . $row['image_id']),
				'U_REPORT'			=> $phpbb_ext_gallery->url->append_sid('mcp', "mode=report_details&amp;album_id=$album_id&amp;option_id=" . $row['report_id']),
				'U_QUEUE'			=> $phpbb_ext_gallery->url->append_sid('mcp', "mode=queue_details&amp;album_id=$album_id&amp;option_id=" . $row['image_id']),
			));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_SORT_DESC'			=> ($sort_dir == 'DESC') ? true : false,
			'S_SORT_KEY'			=> $sort_key,

			'TITLE'					=> $user->lang['IMAGES'],
			'DESCRIPTION'			=> '',//$desc_string,
			'NO_IMAGES_NOTE'		=> $user->lang['NO_IMAGES'],
			'PAGINATION'			=> generate_pagination($phpbb_ext_gallery->url->append_sid('mcp', "mode=$mode&amp;album_id=$album_id&amp;sd=$sort_dir&amp;sk=$sort_key"), $count_images, $images_per_page, $start),
			'PAGE_NUMBER'			=> on_page($count_images, $images_per_page, $start),
			'TOTAL_IMAGES'			=> $user->lang('VIEW_ALBUM_IMAGES', $count_images),

			'S_COMMENTS'			=> $phpbb_ext_gallery->config->get('allow_comments'),
			'S_RATINGS'				=> $phpbb_ext_gallery->config->get('allow_rates'),
			'S_STATUS'				=> true,
			'S_MARK'				=> true,
		));

		$template->assign_vars(array(
			'REPORTED_IMG'				=> $user->img('icon_topic_reported', 'IMAGE_REPORTED'),
			'UNAPPROVED_IMG'			=> $user->img('icon_topic_unapproved', 'IMAGE_UNAPPROVED'),
			'S_MCP_ACTION'				=> $phpbb_ext_gallery->url->append_sid('mcp', "mode=$mode&amp;album_id=$album_id"),
			'DISP_FAKE_THUMB'			=> $phpbb_ext_gallery->config->get('mini_thumbnail_disp'),
			'FAKE_THUMB_SIZE'			=> $phpbb_ext_gallery->config->get('mini_thumbnail_size'),
		));
	}

	static public function details($mode, $option_id, $album_id, $album_data)
	{
		global $db, $template, $user;

		if ($mode == 'queue_details')
		{
			$sql = 'SELECT *
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE image_id = ' . (int) $option_id;
			$result = $db->sql_query_limit($sql, 1);
			$row = $db->sql_fetchrow($result);
			$template->assign_vars(array(
				'IMAGE_STATUS'		=> $row['image_status'],
				'STATUS'			=> $user->lang['QUEUE_STATUS_' . $row['image_status']],
				'REPORT_ID'			=> $row['image_id'],
			));
			$db->sql_freeresult($result);
		}
		else if ($mode == 'report_details')
		{
			$m_status = ' AND i.image_status <> ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED;
			if ($phpbb_ext_gallery->auth->acl_check('m_status', $album_id, $album_data['album_user_id']))
			{
				$m_status = '';
			}

			$sql_array = array(
				'SELECT'		=> 'r.*, u.username reporter_name, u.user_colour reporter_colour, i.*',
				'FROM'			=> array(GALLERY_REPORTS_TABLE => 'r'),

				'LEFT_JOIN'		=> array(
					array(
						'FROM'		=> array(USERS_TABLE => 'u'),
						'ON'		=> 'r.reporter_id = u.user_id',
					),
					array(
						'FROM'		=> array(GALLERY_IMAGES_TABLE => 'i'),
						'ON'		=> 'r.report_image_id = i.image_id',
					),
				),

				'WHERE'			=> 'r.report_id = ' . $option_id . ' ' . $m_status,
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query_limit($sql, 1);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if ($row === false)
			{
				trigger_error('REPORT_NOT_FOUND');
			}

			$template->assign_vars(array(
				'REPORTER'			=> get_username_string('full', $row['reporter_id'], $row['reporter_name'], $row['reporter_colour']),
				'REPORT_TIME'		=> $user->format_date($row['report_time']),
				'REPORT_ID'			=> $row['report_id'],
				'REPORT_NOTE'		=> $row['report_note'],
				'REPORT_STATUS'		=> ($row['report_status'] == phpbb_ext_gallery_core_report::OPEN) ? true : false,
				'STATUS'			=> $user->lang['REPORT_STATUS_' . $row['report_status']] . ' ' . $user->lang['QUEUE_STATUS_' . $row['image_status']],
			));
		}

		$template->assign_vars(array(
			'IMAGE_NAME'		=> $row['image_name'],
			'IMAGE_DESC'		=> generate_text_for_display($row['image_desc'], $row['image_desc_uid'], $row['image_desc_bitfield'], 7),
			'UPLOADER'			=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
			'IMAGE_TIME'		=> $user->format_date($row['image_time']),
			'UC_IMAGE'			=> phpbb_ext_gallery_core_image::generate_link('medium', $phpbb_ext_gallery->config->get('link_thumbnail'), $row['image_id'], $row['image_name'], $album_id),
			'U_EDIT_IMAGE'		=> $phpbb_ext_gallery->url->append_sid('posting', 'mode=edit&amp;album_id=' . $album_id . '&amp;image_id=' . $row['image_id']),
			'U_DELETE_IMAGE'	=> $phpbb_ext_gallery->url->append_sid('posting', 'mode=delete&amp;album_id=' . $album_id . '&amp;image_id=' . $row['image_id']),
			'S_MCP_ACTION'		=> $phpbb_ext_gallery->url->append_sid('mcp', "mode=" . (($mode == 'report_details') ? 'report_open' : 'queue_unapproved') . "&amp;album_id=$album_id"),
		));
	}

	static public function queue($mode, $album_id, $album_data)
	{
		global $config, $db, $template, $user;

		$start				= request_var('start', 0);
		$sort_key			= request_var('sk', 'image_time');
		$sort_dir			= (request_var('sd', 'DESC') == 'DESC') ? 'DESC' : 'ASC';
		$images_per_page	= $config['topics_per_page'];
		$count_images		= 0;

		$use_sort_key = $sort_key;
		if (!in_array($use_sort_key, self::$allowed_sort_params))
		{
			if (in_array($use_sort_key . '_clean', self::$allowed_sort_params))
			{
				$use_sort_key .= '_clean';
			}
			else
			{
				$use_sort_key = 'image_time';
			}
		}

		$where_case = 'AND image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '';
		if ($mode == 'queue_unapproved')
		{
			$where_case = 'AND image_status = ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED;
		}
		else if ($mode == 'queue_approved')
		{
			$where_case = 'AND image_status = ' . phpbb_ext_gallery_core_image::STATUS_APPROVED;
		}
		else if ($mode == 'queue_locked')
		{
			$where_case = 'AND image_status = ' . phpbb_ext_gallery_core_image::STATUS_LOCKED;
		}
		$sql = 'SELECT COUNT(image_id) images
			FROM ' . GALLERY_IMAGES_TABLE . "
			WHERE image_album_id = $album_id
				$where_case";
		$result = $db->sql_query($sql);
		$count_images = (int) $db->sql_fetchfield('images');
		$db->sql_freeresult($result);

		$sql = 'SELECT image_time, image_name, image_id, image_user_id, image_username, image_user_colour
			FROM ' . GALLERY_IMAGES_TABLE . "
			WHERE image_album_id = $album_id
			$where_case
			ORDER BY $use_sort_key $sort_dir";
		$result = $db->sql_query_limit($sql, $images_per_page, $start);
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('image_row', array(
				'THUMBNAIL'			=> phpbb_ext_gallery_core_image::generate_link('fake_thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $row['image_id'], $row['image_name'], $album_id),
				'UPLOADER'			=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
				'IMAGE_TIME'		=> $user->format_date($row['image_time']),
				'IMAGE_NAME'		=> $row['image_name'],
				'IMAGE_ID'			=> $row['image_id'],
				'U_IMAGE'			=> $phpbb_ext_gallery->url->append_sid('image', "album_id=$album_id&amp;image_id=" . $row['image_id']),
				'U_IMAGE_PAGE'		=> $phpbb_ext_gallery->url->append_sid('mcp', "mode=queue_details&amp;album_id=$album_id&amp;option_id=" . $row['image_id']),
			));
		}
		$db->sql_freeresult($result);

		if ($mode == 'queue_unapproved')
		{
			$case = 'UNAPPROVED';
		}
		else if ($mode == 'queue_approved')
		{
			$case = 'APPROVED';
		}
		else if ($mode == 'queue_locked')
		{
			$case = 'LOCKED';
		}
		$desc_string = $user->lang('WAITING_' . $case . '_IMAGE', $count_images);

		$template->assign_vars(array(
			'S_SORT_DESC'			=> ($sort_dir == 'DESC') ? true : false,
			'S_SORT_KEY'			=> $sort_key,

			'TITLE'					=> $user->lang['IMAGES'],
			'DESCRIPTION'			=> $desc_string,
			'PAGINATION'			=> generate_pagination($phpbb_ext_gallery->url->append_sid('mcp', "mode=$mode&amp;album_id=$album_id&amp;sd=$sort_dir&amp;sk=$sort_key"), $count_images, $images_per_page, $start),
			'PAGE_NUMBER'			=> on_page($count_images, $images_per_page, $start),
			'TOTAL_IMAGES'			=> $user->lang('VIEW_ALBUM_IMAGES', $count_images),

			'S_QUEUE_LIST'			=> true,
			'S_MARK'				=> true,
		));

		$template->assign_vars(array(
			'REPORTED_IMG'				=> $user->img('icon_topic_reported', 'IMAGE_REPORTED'),
			'UNAPPROVED_IMG'			=> $user->img('icon_topic_unapproved', 'IMAGE_UNAPPROVED'),
			'S_MCP_ACTION'				=> $phpbb_ext_gallery->url->append_sid('mcp', "mode=$mode&amp;album_id=$album_id"),
			'DISP_FAKE_THUMB'			=> $phpbb_ext_gallery->config->get('mini_thumbnail_disp'),
			'FAKE_THUMB_SIZE'			=> $phpbb_ext_gallery->config->get('mini_thumbnail_size'),
		));
	}

	static public function report($mode, $album_id, $album_data)
	{
		global $config, $db, $template, $user;

		$start				= request_var('start', 0);
		$sort_key			= request_var('sk', 'image_time');
		$sort_dir			= (request_var('sd', 'DESC') == 'DESC') ? 'DESC' : 'ASC';
		$images_per_page	= $config['topics_per_page'];
		$count_images		= 0;
		$use_sort_key = $sort_key;
		if (!in_array($use_sort_key, array_merge(self::$allowed_sort_params, self::$allowed_sort_params_report)))
		{
			if (in_array($use_sort_key . '_clean', array_merge(self::$allowed_sort_params, self::$allowed_sort_params_report)))
			{
				$use_sort_key .= '_clean';
			}
			else
			{
				$use_sort_key = 'image_time';
			}
		}

		$m_status = ' AND i.image_status <> ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED;
		if ($phpbb_ext_gallery->auth->acl_check('m_status', $album_id, $album_data['album_user_id']))
		{
			$m_status = '';
		}

		if ($mode == 'report_open')
		{
			$report_status = phpbb_ext_gallery_core_report::OPEN;
		}
		else
		{
			$report_status = phpbb_ext_gallery_core_report::LOCKED;
		}

		$sql_array = array(
			'SELECT'		=> 'COUNT(i.image_id) images',
			'FROM'			=> array(GALLERY_REPORTS_TABLE => 'r'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array(GALLERY_IMAGES_TABLE => 'i'),
					'ON'		=> 'r.report_image_id = i.image_id',
				),
			),

			'WHERE'			=> "r.report_album_id = $album_id
								AND i.image_status <> " . phpbb_ext_gallery_core_image::STATUS_ORPHAN . "
								AND r.report_status = $report_status $m_status",
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$count_images = (int) $db->sql_fetchfield('images');
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

			'WHERE'			=> "r.report_album_id = $album_id AND r.report_status = $report_status $m_status",
			'ORDER_BY'		=> $use_sort_key . ' ' . $sort_dir,
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query_limit($sql, $images_per_page, $start);

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('image_row', array(
				'THUMBNAIL'			=> phpbb_ext_gallery_core_image::generate_link('fake_thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $row['image_id'], $row['image_name'], $album_id),
				'REPORTER'			=> get_username_string('full', $row['reporter_id'], $row['reporter_name'], $row['reporter_colour']),
				'UPLOADER'			=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
				'REPORT_ID'			=> $row['report_id'],
				'REPORT_MOD'		=> ($row['report_manager']) ? get_username_string('full', $row['report_manager'], $row['mod_username'], $row['mod_user_colour']) : '',
				'REPORT_TIME'		=> $user->format_date($row['report_time']),
				'IMAGE_TIME'		=> $user->format_date($row['image_time']),
				'IMAGE_NAME'		=> $row['image_name'],
				'U_IMAGE'			=> $phpbb_ext_gallery->url->append_sid('image', "album_id=$album_id&amp;image_id=" . $row['image_id']),
				'U_IMAGE_PAGE'		=> $phpbb_ext_gallery->url->append_sid('mcp', 'mode=report_details&amp;album_id=' . $album_id . '&amp;option_id=' . $row['report_id']),
			));
		}
		$db->sql_freeresult($result);

		if ($report_status == phpbb_ext_gallery_core_report::LOCKED)
		{
			$desc_string = $user->lang('WAITING_REPORTED_DONE', $count_images);
		}
		else
		{
			$desc_string = $user->lang('WAITING_REPORTED_IMAGE', $count_images);
		}


		$template->assign_vars(array(
			'S_SORT_DESC'			=> ($sort_dir == 'DESC') ? true : false,
			'S_SORT_KEY'			=> $sort_key,

			'TITLE'					=> $user->lang['REPORTED_IMAGES'],
			'DESCRIPTION'			=> $desc_string,
			'PAGINATION'			=> generate_pagination($phpbb_ext_gallery->url->append_sid('mcp', "mode=$mode&amp;album_id=$album_id&amp;sd=$sort_dir&amp;sk=$sort_key"), $count_images, $images_per_page, $start),
			'PAGE_NUMBER'			=> on_page($count_images, $images_per_page, $start),
			'TOTAL_IMAGES'			=> $user->lang('VIEW_ALBUM_IMAGES', $count_images),

			'S_REPORT_LIST'			=> true,
			'S_REPORTER'			=> true,
			'S_MARK'				=> true,
		));

		$template->assign_vars(array(
			'REPORTED_IMG'				=> $user->img('icon_topic_reported', 'IMAGE_REPORTED'),
			'UNAPPROVED_IMG'			=> $user->img('icon_topic_unapproved', 'IMAGE_UNAPPROVED'),
			'S_MCP_ACTION'				=> $phpbb_ext_gallery->url->append_sid('mcp', "mode=$mode&amp;album_id=$album_id"),
			'DISP_FAKE_THUMB'			=> $phpbb_ext_gallery->config->get('mini_thumbnail_disp'),
			'FAKE_THUMB_SIZE'			=> $phpbb_ext_gallery->config->get('mini_thumbnail_size'),
		));
	}
}
