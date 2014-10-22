<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* partially borrowed from phpBB3
* @author: phpBB Group
* @location: search.php
*/

/**
* @ignore
*/

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include('common.' . $phpEx);
include($phpbb_root_path . 'common.' . $phpEx);

$phpbb_ext_gallery = new phpbb_ext_gallery_core($auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path);
$phpbb_ext_gallery->setup('search');
$phpbb_ext_gallery->url->_include('functions_display', 'phpbb');

// Define initial vars
//@todo: $mode			= request_var('mode', '');
$search_id		= request_var('search_id', '');
$start			= request_var('start', 0);
$image_id		= request_var('image_id', 0);

$submit			= request_var('submit', false);
$keywords		= utf8_normalize_nfc(request_var('keywords', '', true));
$add_keywords	= utf8_normalize_nfc(request_var('add_keywords', '', true));
$username		= request_var('username', '', true);
$user_id		= request_var('user_id', 0);
$search_terms	= request_var('terms', 'all');
$search_album	= request_var('aid', array(0));
$search_child	= request_var('sc', true);
$search_fields	= request_var('sf', 'all');
$sort_days		= request_var('st', 0);
$sort_key		= request_var('sk', 't');
$sort_dir		= request_var('sd', 'd');


// Is user able to search? Has search been disabled?
if (!$auth->acl_get('u_search') || !$config['load_search'])
{
	$template->assign_var('S_NO_SEARCH', true);
	trigger_error('NO_SEARCH');
}


$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['SEARCH'],
	'U_VIEW_FORUM'	=> $phpbb_ext_gallery->url->append_sid('search'),
));

// Define some vars
$images_per_page = $phpbb_ext_gallery->config->get('album_rows') * $phpbb_ext_gallery->config->get('album_columns');
$tot_unapproved = $image_counter = 0;

/**
* Build the sort options
*/
$limit_days = array(0 => $user->lang['ALL_IMAGES'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);
$sort_by_text = array('t' => $user->lang['TIME'], 'n' => $user->lang['IMAGE_NAME'], 'u' => $user->lang['SORT_USERNAME'], 'vc' => $user->lang['GALLERY_VIEWS']);
$sort_by_sql = array('t' => 'image_time', 'n' => 'image_name_clean', 'u' => 'image_username_clean', 'vc' => 'image_view_count');

if ($phpbb_ext_gallery->config->get('allow_rates'))
{
	$sort_by_text['ra'] = $user->lang['RATING'];
	$sort_by_sql['ra'] = 'image_rate_points';//@todo: (phpbb_gallery_contest::$mode == phpbb_gallery_contest::MODE_SUM) ? 'image_rate_points' : 'image_rate_avg';
	$sort_by_text['r'] = $user->lang['RATES_COUNT'];
	$sort_by_sql['r'] = 'image_rates';
}
if ($phpbb_ext_gallery->config->get('allow_comments'))
{
	$sort_by_text['c'] = $user->lang['COMMENTS'];
	$sort_by_sql['c'] = 'image_comments';
	$sort_by_text['lc'] = $user->lang['NEW_COMMENT'];
	$sort_by_sql['lc'] = 'image_last_comment';
}

$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);
$sql_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

/**
* Search
*/
if ($keywords || $username || $user_id || $search_id || $submit)
{
	// clear arrays
	$id_ary = array();

	// This is what our Search could so far
	if ($user_id)
	{
		$search_id = 'usersearch';
	}

	// egosearch is an user search
	if ($search_id == 'egosearch')
	{
		$user_id = $user->data['user_id'];

		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box('', $user->lang['LOGIN_EXPLAIN_EGOSEARCH']);
		}
	}

	// If we are looking for authors get their ids
	$user_id_ary = array();
	if ($username)
	{
		if ((strpos($username, '*') !== false) && (utf8_strlen(str_replace(array('*', '%'), '', $username)) < $config['min_search_author_chars']))
		{
			trigger_error(sprintf($user->lang['TOO_FEW_AUTHOR_CHARS'], $config['min_search_author_chars']));
		}

		$sql_where = (strpos($username, '*') !== false) ? ' username_clean ' . $db->sql_like_expression(str_replace('*', $db->any_char, utf8_clean_string($username))) : " username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'";

		// Missing images and comments of guests/deleted users
		$sql = 'SELECT user_id
			FROM ' . USERS_TABLE . "
			WHERE $sql_where
				AND user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
		$result = $db->sql_query_limit($sql, 100);

		while ($row = $db->sql_fetchrow($result))
		{
			$user_id_ary[] = (int) $row['user_id'];
		}
		$db->sql_freeresult($result);
		$user_id_ary[] = (int) ANONYMOUS;

		/**
		* Allow Search for guests/deleted users
		if (!sizeof($user_id_ary))
		{
			trigger_error('NO_SEARCH_RESULTS');
		}
		*/
	}

	// if we search in an existing search result just add the additional keywords. But we need to use "all search terms"-mode
	// so we can keep the old keywords in their old mode, but add the new ones as required words
	if ($add_keywords)
	{
		if ($search_terms == 'all')
		{
			$keywords .= ' ' . $add_keywords;
		}
		else
		{
			$search_terms = 'all';
			$keywords = preg_replace('#\s+#u', ' |', $keywords) . ' ' .$add_keywords;
		}
	}
	$keywords_ary = ($keywords) ? explode(' ', $keywords) : array();

	// pre-made searches
	$sql = $field = $l_search_title = $search_results = '';

	$total_match_count = 0;
	$sql_limit = 0;

	// Special searches: recent, random, toprated, ...
	if ($search_id)
	{
		switch ($search_id)
		{
			case 'recent':
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['SEARCH_RECENT'],
					'U_VIEW_FORUM'	=> $phpbb_ext_gallery->url->append_sid('search', 'search_id=' . $search_id),
				));

				$l_search_title = $user->lang['SEARCH_RECENT'];
				$search_results = 'image';

				$sql_order = 'image_id DESC';
				$sql_limit = phpbb_ext_gallery_core::SEARCH_PAGES_NUMBER * $images_per_page;
				$sql = 'SELECT image_id
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND ((' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('i_view'), false, true) . ' AND image_status <> ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . ')
							OR ' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('m_status'), false, true) . ')
					ORDER BY ' . $sql_order;
			break;

			case 'random':
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['SEARCH_RANDOM'],
					'U_VIEW_FORUM'	=> $phpbb_ext_gallery->url->append_sid('search', 'search_id=' . $search_id),
				));

				$l_search_title = $user->lang['SEARCH_RANDOM'];
				$search_results = 'image';

				switch ($db->sql_layer)
				{
					case 'postgres':
						$sql_order = 'RANDOM()';
					break;

					case 'mssql':
					case 'mssql_odbc':
						$sql_order = 'NEWID()';
					break;

					default:
						$sql_order = 'RAND()';
					break;
				}
				$sql_limit = $images_per_page;
				$sql = 'SELECT image_id
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND ((' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('i_view'), false, true) . ' AND image_status <> ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . ')
							OR ' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('m_status'), false, true) . ')
					ORDER BY ' . $sql_order;
			break;

			case 'commented':
			if ($phpbb_ext_gallery->config->get('allow_comments'))
			{
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['SEARCH_RECENT_COMMENTS'],
					'U_VIEW_FORUM'	=> $phpbb_ext_gallery->url->append_sid('search', 'search_id=' . $search_id),
				));

				$l_search_title = $user->lang['SEARCH_RECENT_COMMENTS'];
				$search_results = 'comment';

				$sql_order = 'c.comment_id DESC';
				$sql_limit = phpbb_ext_gallery_core::SEARCH_PAGES_NUMBER * $images_per_page;

				$sql_array = array(
					'SELECT'		=> 'c.comment_id',
					'FROM'			=> array(GALLERY_COMMENTS_TABLE => 'c'),

					'LEFT_JOIN'		=> array(
						array(
							'FROM'		=> array(GALLERY_IMAGES_TABLE => 'i'),
							'ON'		=> 'c.comment_image_id = i.image_id',
						),
					),

					'WHERE'			=> '((' . $db->sql_in_set('i.image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('i_view'), false, true) . ' AND i.image_status <> ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . ')
						OR ' . $db->sql_in_set('i.image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('m_status'), false, true) . ')
						AND ' . $db->sql_in_set('i.image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('c_read'), false, true),
					'ORDER_BY'		=> $sql_order,
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
			}
			break;

			case 'toprated':
			if ($phpbb_ext_gallery->config->get('allow_rates'))
			{
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['SEARCH_TOPRATED'],
					'U_VIEW_FORUM'	=> $phpbb_ext_gallery->url->append_sid('search', 'search_id=' . $search_id),
				));

				$l_search_title = $user->lang['SEARCH_TOPRATED'];
				$search_results = 'image';

				$sql_order = 'image_rate_avg DESC';
				$sql_limit = phpbb_ext_gallery_core::SEARCH_PAGES_NUMBER * $images_per_page;
				// We need to hide contest-images on this search_id, if the contest is still running!
				$sql = 'SELECT image_id
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND image_rate_points <> 0
						AND ((' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('i_view'), false, true) . ' AND image_status <> ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . ' AND image_contest = ' . phpbb_ext_gallery_core_image::NO_CONTEST . ')
							OR ' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('m_status'), false, true) . ')
					ORDER BY ' . $sql_order;
			}
			break;

			case 'contests':
			if ($phpbb_ext_gallery->config->get('allow_rates'))
			{
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['SEARCH_CONTEST'],
					'U_VIEW_FORUM'	=> $phpbb_ext_gallery->url->append_sid('search', 'search_id=' . $search_id),
				));

				$l_search_title = $user->lang['SEARCH_CONTEST'];
				$search_results = 'image';

				$sql_array = array(
					'SELECT'		=> 'c.*, a.album_name',
					'FROM'			=> array(GALLERY_CONTESTS_TABLE => 'c'),

					'LEFT_JOIN'		=> array(
						array(
							'FROM'		=> array(GALLERY_ALBUMS_TABLE => 'a'),
							'ON'		=> 'a.album_id = c.contest_album_id',
						),
					),

					'WHERE'			=> $db->sql_in_set('c.contest_album_id', array_unique(array_merge($phpbb_ext_gallery->auth->acl_album_ids('i_view'), $phpbb_ext_gallery->auth->acl_album_ids('m_status'))), false, true) . ' AND c.contest_marked = ' . phpbb_gallery_image::NO_CONTEST,
					'ORDER_BY'		=> 'c.contest_start + c.contest_end DESC',
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query_limit($sql, $phpbb_ext_gallery->config->get('album_rows') * phpbb_ext_gallery_core::SEARCH_PAGES_NUMBER);

				while ($row = $db->sql_fetchrow($result))
				{
					$id_ary[] = $row['contest_first'];
					$id_ary[] = $row['contest_second'];
					$id_ary[] = $row['contest_third'];
					$contest_images[$row['contest_id']] = array(
						'album_id'		=> $row['contest_album_id'],
						'album_name'	=> $row['album_name'],
						'images'		=> array($row['contest_first'], $row['contest_second'], $row['contest_third'])
					);
				}
				$db->sql_freeresult($result);

				// Clear $sql, so we do not execute it again.
				$sql = '';
			}
			break;

			case 'egosearch':
				$user_id = $user->data['user_id'];

				// no break

			case 'usersearch':
				// Get username for the search-title "Images of %s"
				$sql = 'SELECT username
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . $user_id;
				$result = $db->sql_query($sql);
				$username = $db->sql_fetchfield('username');
				$db->sql_freeresult($result);

				$l_search_title = sprintf($user->lang['SEARCH_USER_IMAGES_OF'], $username);
				$search_results = 'image';

				$sql_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
				// We need to hide contest-images on this search_id, if the contest is still running!
				$sql = 'SELECT image_id
					FROM ' . GALLERY_IMAGES_TABLE . '
					WHERE image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
						AND image_user_id = ' . $user_id . '
						AND ((' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('i_view'), false, true) . ' AND image_status <> ' . phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . ' AND image_contest = ' . phpbb_ext_gallery_core_image::NO_CONTEST . ')
							OR ' . $db->sql_in_set('image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('m_status'), false, true) . ')
					ORDER BY ' . $sql_order;
			break;
		}
	}
	// "Normal" search
	else
	{
		$search_query = '';
		$matches = array('i.image_name', 'i.image_desc');

		if (!sizeof($keywords_ary) && !sizeof($user_id_ary))
		{
			trigger_error('NO_SEARCH_RESULTS');
		}
		foreach ($keywords_ary as $word)
		{
			$match_search_query = '';
			foreach ($matches as $match)
			{
				$match_search_query .= (($match_search_query) ? ' OR ' : '') . 'LOWER('. $match . ') ';
				$match_search_query .= $db->sql_like_expression(str_replace('*', $db->any_char, $db->any_char . strtolower($word) . $db->any_char));
			}
			$search_query .= ((!$search_query) ? '' : (($search_terms == 'all') ? ' AND ' : ' OR ')) . '(' . $match_search_query . ')';
		}

		$search_results = 'image';

		$sql_limit = phpbb_ext_gallery_core::SEARCH_PAGES_NUMBER * $images_per_page;
		$sql_match = 'i.image_name';
		$sql_where_options = '';

		$sql = 'SELECT i.image_id
			FROM ' . GALLERY_IMAGES_TABLE . ' i
			WHERE i.image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
				AND ((' . $db->sql_in_set('i.image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('i_view'), false, true) . ' AND i.image_status <> ' . phpbb_gallery_image::STATUS_UNAPPROVED . ')
					OR ' . $db->sql_in_set('i.image_album_id', $phpbb_ext_gallery->auth->acl_album_ids('m_status'), false, true) . ')
				' . (($search_query) ? 'AND (' . $search_query . ')' : '') . '
				' . (($user_id_ary) ? ' AND ' . $db->sql_in_set('i.image_user_id', $user_id_ary) : '') . '
				' . (($search_album) ? ' AND ' . $db->sql_in_set('i.image_album_id', $search_album) : '') . '
			ORDER BY ' . $sql_order;
	}

	if ($sql)
	{
		if (!$sql_limit)
		{
			$result = $db->sql_query($sql);
		}
		else
		{
			$result = $db->sql_query_limit($sql, $sql_limit);
		}

		while ($row = $db->sql_fetchrow($result))
		{
			$id_ary[] = $row[$search_results . '_id'];
		}
		$db->sql_freeresult($result);

		$total_match_count = sizeof($id_ary);
		$id_ary = array_slice($id_ary, $start, $images_per_page);
	}

	$l_search_matches = $user->lang('FOUND_SEARCH_MATCHES', $total_match_count);

	// For some searches we need to print out the "no results" page directly to allow re-sorting/refining the search options.
	if (!sizeof($id_ary))
	{
		trigger_error('NO_SEARCH_RESULTS');
	}

	$sql_where = '';

	if (sizeof($id_ary))
	{
		$sql_where .= ($search_results == 'image') ? $db->sql_in_set('i.image_id', $id_ary) : $db->sql_in_set('c.comment_id', $id_ary);
	}

	// define some vars for urls
	$hilit = explode(' ', preg_replace('#\s+#u', ' ', str_replace(array('+', '-', '|', '(', ')', '&quot;'), ' ', $keywords)));
	$searchwords = implode(', ', $hilit);
	$hilit = implode('|', $hilit);
	// Do not allow *only* wildcard being used for hilight
	$hilit = (strspn($hilit, '*') === strlen($hilit)) ? '' : $hilit;

	$u_hilit = urlencode(htmlspecialchars_decode(str_replace('|', ' ', $hilit)));
	$u_search_album = implode('&amp;aid%5B%5D=', $search_album);

	$u_search = $phpbb_ext_gallery->url->append_sid('search', $u_sort_param);
	$u_search .= ($search_id) ? '&amp;search_id=' . $search_id : '';
	//@todo: 
	$u_search .= ($search_terms != 'all') ? '&amp;terms=' . $search_terms : '';
	$u_search .= ($u_hilit) ? '&amp;keywords=' . urlencode(htmlspecialchars_decode($keywords)) : '';
	$u_search .= ($username) ? '&amp;username=' . urlencode(htmlspecialchars_decode($username)) : '';
	$u_search .= ($user_id) ? '&amp;user_id=' . $user_id : '';
	$u_search .= ($u_search_album) ? '&amp;aid%5B%5D=' . $u_search_album : '';
	$u_search .= (!$search_child) ? '&amp;sc=0' : '';
	$u_search .= ($search_fields != 'all') ? '&amp;sf=' . $search_fields : '';

	phpbb_generate_template_pagination($template, $u_search, 'pagination', 'start', $total_match_count, $images_per_page, $start);

	$template->assign_vars(array(
		'SEARCH_MATCHES'	=> $l_search_matches,
		'PAGE_NUMBER'		=> phpbb_on_page($template, $user, $u_search, $total_match_count, $images_per_page, $start),

		'SEARCH_TITLE'		=> $l_search_title,
		'SEARCH_WORDS'		=> $searchwords,
		//@todo: 'IGNORED_WORDS'		=> (sizeof($search->common_words)) ? implode(' ', $search->common_words) : '',
		'TOTAL_MATCHES'		=> $total_match_count,
		'SEARCH_IN_RESULTS'	=> ($search_id) ? false : true,

		'S_SELECT_SORT_DIR'		=> $s_sort_dir,
		'S_SELECT_SORT_KEY'		=> $s_sort_key,
		'S_SELECT_SORT_DAYS'	=> $s_limit_days,
		'S_SEARCH_ACTION'		=> $u_search,

		'U_SEARCH_WORDS'	=> $u_search,
		'SEARCH_IMAGES'		=> ($search_results == 'image') ? true : false,
		'S_THUMBNAIL_SIZE'	=> $phpbb_ext_gallery->config->get('thumbnail_height') + 20 + (($phpbb_ext_gallery->config->get('thumbnail_infoline')) ? phpbb_ext_gallery_core_image::THUMBNAIL_INFO_HEIGHT : 0),
	));

	if ($sql_where)
	{
		// Search results are images
		if ($search_results == 'image')
		{
			$sql_array = array(
				'SELECT'		=> 'i.*, a.album_name, a.album_status, a.album_user_id',
				'FROM'			=> array(GALLERY_IMAGES_TABLE => 'i'),

				'LEFT_JOIN'		=> array(
					array(
						'FROM'		=> array(GALLERY_ALBUMS_TABLE => 'a'),
						'ON'		=> 'a.album_id = i.image_album_id',
					),
				),

				'WHERE'			=> 'i.image_status <> ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . ' AND ' . $sql_where,
				'ORDER_BY'		=> $sql_order,
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$rowset = array();

			while ($row = $db->sql_fetchrow($result))
			{
				if ($search_id == 'contests')
				{
					$rowset[$row['image_id']] = $row;
				}
				else
				{
					$rowset[] = $row;
				}
			}
			$db->sql_freeresult($result);

			$columns_per_page = $phpbb_ext_gallery->config->get('album_columns'); //@todo: ($search_id == 'contests') ? phpbb_gallery_contest::NUM_IMAGES : $phpbb_ext_gallery->config->get('album_columns');
			$init_block = true;
			if ($search_id == 'contests')
			{
				foreach ($contest_images as $contest => $contest_data)
				{
					$num = 0;
					$template->assign_block_vars('imageblock', array(
						'U_BLOCK'			=> $phpbb_ext_gallery->url->append_sid('album', 'album_id=' . $contest_data['album_id'] . '&amp;sk=ra&amp;sd=d'),
						'BLOCK_NAME'		=> sprintf($user->lang['CONTEST_WINNERS_OF'], $contest_data['album_name']),
						'S_CONTEST_BLOCK'	=> true,
						'S_COL_WIDTH'		=> '33%',
						'S_COLS'			=> 3,
					));
					foreach ($contest_data['images'] as $contest_image)
					{
						if (($num % phpbb_gallery_contest::NUM_IMAGES) == 0)
						{
							$template->assign_block_vars('imageblock.imagerow', array());
						}
						if (!empty($rowset[$contest_image]))
						{
							phpbb_ext_gallery_core_image::assign_block('imageblock.imagerow.image', $rowset[$contest_image], $rowset[$contest_image]['album_status'], $phpbb_ext_gallery->config->get('search_display'), $rowset[$contest_image]['album_user_id']);
							$num++;
						}
					}
					while (($num % phpbb_gallery_contest::NUM_IMAGES) > 0)
					{
						$template->assign_block_vars('imageblock.imagerow.no_image', array());
						$num++;
					}
				}
			}
			else
			{
				for ($i = 0, $end = count($rowset); $i < $end; $i += $columns_per_page)
				{
					if ($init_block)
					{
						$template->assign_block_vars('imageblock', array(
							'U_BLOCK'		=> $u_search,
							'BLOCK_NAME'	=> ($l_search_title) ? $l_search_title : $l_search_matches,
							'S_COL_WIDTH'	=> (100 / $phpbb_ext_gallery->config->get('album_columns')) . '%',
							'S_COLS'		=> $phpbb_ext_gallery->config->get('album_columns'),
						));
						$init_block = false;
					}
					$template->assign_block_vars('imageblock.imagerow', array());

					for ($j = $i, $end_columns = ($i + $columns_per_page); $j < $end_columns; $j++)
					{
						if ($j >= $end)
						{
							$template->assign_block_vars('imageblock.imagerow.noimage', array());
							continue;
						}

						// Assign the image to the template-block
						phpbb_ext_gallery_core_image::assign_block('imageblock.imagerow.image', $rowset[$j], $rowset[$j]['album_status'], $phpbb_ext_gallery->config->get('search_display'), $rowset[$j]['album_user_id']);
					}
				}
			}
		}
		// Search results are comments
		else
		{
			$sql_array = array(
				'SELECT'		=> 'c.*, i.*',
				'FROM'			=> array(GALLERY_COMMENTS_TABLE => 'c'),

				'LEFT_JOIN'		=> array(
					array(
						'FROM'		=> array(GALLERY_IMAGES_TABLE => 'i'),
						'ON'		=> 'c.comment_image_id = i.image_id',
					),
				),

				'WHERE'			=> $sql_where,
				'ORDER_BY'		=> $sql_order,
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);

			while ($commentrow = $db->sql_fetchrow($result))
			{
				$image_id = $commentrow['image_id'];
				$album_id = $commentrow['image_album_id'];

				$template->assign_block_vars('commentrow', array(
					'U_COMMENT'		=> $phpbb_ext_gallery->url->append_sid('image_page', "album_id=$album_id&amp;image_id=$image_id") . '#' . $commentrow['comment_id'],
					'COMMENT_ID'	=> $commentrow['comment_id'],
					'TIME'			=> $user->format_date($commentrow['comment_time']),
					'TEXT'			=> generate_text_for_display($commentrow['comment'], $commentrow['comment_uid'], $commentrow['comment_bitfield'], 7),
					'U_DELETE'		=> ($phpbb_ext_gallery->auth->acl_check('m_comments', $album_id) || ($phpbb_ext_gallery->auth->acl_check('c_delete', $album_id) && ($commentrow['comment_user_id'] == $user->data['user_id']) && $user->data['is_registered'])) ? $phpbb_ext_gallery->url->append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=delete&amp;comment_id=" . $commentrow['comment_id']) : '',
					'U_QUOTE'		=> ($phpbb_ext_gallery->auth->acl_check('c_post', $album_id)) ? $phpbb_ext_gallery->url->append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=add&amp;comment_id=" . $commentrow['comment_id']) : '',
					'U_EDIT'		=> ($phpbb_ext_gallery->auth->acl_check('m_comments', $album_id) || ($phpbb_ext_gallery->auth->acl_check('c_edit', $album_id) && ($commentrow['comment_user_id'] == $user->data['user_id']) && $user->data['is_registered'])) ? $phpbb_ext_gallery->url->append_sid('comment', "album_id=$album_id&amp;image_id=$image_id&amp;mode=edit&amp;comment_id=" . $commentrow['comment_id']) : '',
					'U_INFO'		=> ($auth->acl_get('a_')) ? $phpbb_ext_gallery->url->append_sid('mcp', 'mode=whois&amp;ip=' . $commentrow['comment_user_ip']) : '',

					'UC_THUMBNAIL'			=> phpbb_ext_gallery_core_image::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $commentrow['image_id'], $commentrow['image_name'], $commentrow['image_album_id']),
					'UC_IMAGE_NAME'			=> phpbb_ext_gallery_core_image::generate_link('image_name', $phpbb_ext_gallery->config->get('link_image_name'), $commentrow['image_id'], $commentrow['image_name'], $commentrow['image_album_id']),
					'IMAGE_AUTHOR'			=> get_username_string('full', $commentrow['image_user_id'], $commentrow['image_username'], $commentrow['image_user_colour']),
					'IMAGE_TIME'			=> $user->format_date($commentrow['image_time']),

					'POST_AUTHOR_FULL'		=> get_username_string('full', $commentrow['comment_user_id'], $commentrow['comment_username'], $commentrow['comment_user_colour']),
					'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $commentrow['comment_user_id'], $commentrow['comment_username'], $commentrow['comment_user_colour']),
					'POST_AUTHOR'			=> get_username_string('username', $commentrow['comment_user_id'], $commentrow['comment_username'], $commentrow['comment_user_colour']),
					'U_POST_AUTHOR'			=> get_username_string('profile', $commentrow['comment_user_id'], $commentrow['comment_username'], $commentrow['comment_user_colour']),
				));
			}
			$db->sql_freeresult($result);

			$template->assign_vars(array(
				'DELETE_IMG'		=> $user->img('icon_post_delete', 'DELETE_COMMENT'),
				'EDIT_IMG'			=> $user->img('icon_post_edit', 'EDIT_COMMENT'),
				'QUOTE_IMG'			=> $user->img('icon_post_quote', 'QUOTE_COMMENT'),
				'INFO_IMG'			=> $user->img('icon_post_info', 'IP'),
				'MINI_POST_IMG'		=> $user->img('icon_post_target', 'COMMENT'),
				'PROFILE_IMG'		=> $user->img('icon_user_profile', 'READ_PROFILE'),
			));
		}
	}
	unset($rowset);

	page_header(($l_search_title) ? $l_search_title : $user->lang['SEARCH']);

	$template->set_filenames(array(
		'body' => 'gallery/search_results.html')
	);
	make_jumpbox($phpbb_ext_gallery->url->append_sid('phpbb', 'viewforum'));

	page_footer();
}

$s_albums = phpbb_ext_gallery_core_album::get_albumbox(false, false, false, 'i_view' /*'a_search'*/);
if (!$s_albums)
{
	trigger_error('NO_SEARCH');
}

// Prevent undefined variable on build_hidden_fields()
$s_hidden_fields = array('e' => 0);

if (false)//@todo: phpbb_gallery::$display_popup)
{
	$s_hidden_fields['display'] = 'popup';
}
if ($_SID)
{
	$s_hidden_fields['sid'] = $_SID;
}

if (!empty($_EXTRA_URL))
{
	foreach ($_EXTRA_URL as $url_param)
	{
		$url_param = explode('=', $url_param, 2);
		$s_hidden_fields[$url_param[0]] = $url_param[1];
	}
}

$template->assign_vars(array(
	'S_SEARCH_ACTION'		=> $phpbb_ext_gallery->url->append_sid('search', false, true, 0), // We force no ?sid= appending by using 0
	'S_HIDDEN_FIELDS'		=> build_hidden_fields($s_hidden_fields),
	'S_ALBUM_OPTIONS'		=> $s_albums,
	'S_SELECT_SORT_DIR'		=> $s_sort_dir,
	'S_SELECT_SORT_KEY'		=> $s_sort_key,
	'S_SELECT_SORT_DAYS'	=> $s_limit_days,
	'S_IN_SEARCH'			=> true,
));

page_header($user->lang['GALLERY'] . ' &bull; ' . $user->lang['SEARCH'], false);

$template->set_filenames(array(
	'body' => 'gallery/search_body.html')
);

page_footer();
