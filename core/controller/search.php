<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 Luifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\controller;

class search
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\db\driver\driver */
	protected $db;

	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbbgallery\core\album\display */
	protected $display;

	/* @var string */
	protected $root_path;

	/* @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth			$auth		Auth object
	* @param \phpbb\config\config		$config		Config object
	* @param \phpbb\db\driver\driver	$db			Database object
	* @param \phpbb\request\request		$request	Request object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user				$user		User object
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbbgallery\core\album\display	$display	Albums display object
	* @param string						$root_path	Root path
	* @param string						$php_ext	php file extension
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\pagination $pagination, \phpbb\request\request $request, 
	\phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbbgallery\core\album\display $display, \phpbbgallery\core\config $gallery_config, 
	\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\album\album $album, \phpbbgallery\core\image\image $image, \phpbbgallery\core\url $url, \phpbbgallery\core\search $gallery_search,
	$images_table, $albums_table, $comments_table, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->display = $display;
		$this->gallery_config = $gallery_config;
		$this->gallery_auth = $gallery_auth;
		$this->album = $album;
		$this->image = $image;
		$this->url = $url;
		$this->gallery_search = $gallery_search;
		$this->images_table = $images_table;
		$this->albums_table = $albums_table;
		$this->comments_table = $comments_table;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Index Controller
	*	Route: gallery/search
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	
	public function base()
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('search');
		
		//search 
		
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
		if (!$this->auth->acl_get('u_search') || !$this->config['load_search'])
		{
			$this->template->assign_var('S_NO_SEARCH', true);
			trigger_error('NO_SEARCH');
		}
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_search'),
		));

		// Define some vars
		$images_per_page = $this->gallery_config->get('album_rows') * $this->gallery_config->get('album_columns');
		$tot_unapproved = $image_counter = 0;

		/**
		* Build the sort options
		*/
		$limit_days = array(0 => $this->user->lang['ALL_IMAGES'], 1 => $this->user->lang['1_DAY'], 7 => $this->user->lang['7_DAYS'], 14 => $this->user->lang['2_WEEKS'], 30 => $this->user->lang['1_MONTH'], 90 => $this->user->lang['3_MONTHS'], 180 => $this->user->lang['6_MONTHS'], 365 => $this->user->lang['1_YEAR']);
		$sort_by_text = array('t' => $this->user->lang['TIME'], 'n' => $this->user->lang['IMAGE_NAME'], 'u' => $this->user->lang['SORT_USERNAME'], 'vc' => $this->user->lang['GALLERY_VIEWS']);
		$sort_by_sql = array('t' => 'image_time', 'n' => 'image_name_clean', 'u' => 'image_username_clean', 'vc' => 'image_view_count');

		if ($this->gallery_config->get('allow_rates'))
		{
			$sort_by_text['ra'] = $this->user->lang['RATING'];
			$sort_by_sql['ra'] = 'image_rate_points';//@todo: (phpbb_gallery_contest::$mode == phpbb_gallery_contest::MODE_SUM) ? 'image_rate_points' : 'image_rate_avg';
			$sort_by_text['r'] = $this->user->lang['RATES_COUNT'];
			$sort_by_sql['r'] = 'image_rates';
		}
		if ($this->gallery_config->get('allow_comments'))
		{
			$sort_by_text['c'] = $this->user->lang['COMMENTS'];
			$sort_by_sql['c'] = 'image_comments';
			$sort_by_text['lc'] = $this->user->lang['NEW_COMMENT'];
			$sort_by_sql['lc'] = 'image_last_comment';
		}
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);
		$sql_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
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
				$user_id = $this->user->data['user_id'];

				if ($this->user->data['user_id'] == ANONYMOUS)
				{
					login_box('', $user->lang['LOGIN_EXPLAIN_EGOSEARCH']);
				}
			}

			// If we are looking for authors get their ids
			$user_id_ary = array();
			if ($username)
			{
				if ((strpos($username, '*') !== false) && (utf8_strlen(str_replace(array('*', '%'), '', $username)) < $this->config['min_search_author_chars']))
				{
					trigger_error(sprintf($this->user->lang['TOO_FEW_AUTHOR_CHARS'], $this->config['min_search_author_chars']));
				}

				$sql_where = (strpos($username, '*') !== false) ? ' username_clean ' . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), utf8_clean_string($username))) : " username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'";

				// Missing images and comments of guests/deleted users
				$sql = 'SELECT user_id
					FROM ' . USERS_TABLE . "
					WHERE $sql_where
						AND user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
				$result = $this->db->sql_query_limit($sql, 100);

				while ($row = $this->db->sql_fetchrow($result))
				{
					$user_id_ary[] = (int) $row['user_id'];
				}
				$this->db->sql_freeresult($result);
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
					$match_search_query .= $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), $this->db->get_any_char() . strtolower($word) . $this->db->get_any_char()));
				}
				$search_query .= ((!$search_query) ? '' : (($search_terms == 'all') ? ' AND ' : ' OR ')) . '(' . $match_search_query . ')';
			}

			$search_results = 'image';

			$sql_limit = \phpbbgallery\core\core::SEARCH_PAGES_NUMBER * $images_per_page;
			$sql_match = 'i.image_name';
			$sql_where_options = '';

			$sql = 'SELECT i.image_id
				FROM ' . $this->images_table . ' i
				WHERE i.image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN . '
					AND ((' . $this->db->sql_in_set('i.image_album_id', $this->gallery_auth->acl_album_ids('i_view'), false, true) . ' AND i.image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
						OR ' . $this->db->sql_in_set('i.image_album_id', $this->gallery_auth->acl_album_ids('m_status'), false, true) . ')
					' . (($search_query) ? 'AND (' . $search_query . ')' : '') . '
					' . (($user_id_ary) ? ' AND ' . $this->db->sql_in_set('i.image_user_id', $user_id_ary) : '') . '
					' . (($search_album) ? ' AND ' . $this->db->sql_in_set('i.image_album_id', $search_album) : '') . '
				ORDER BY ' . $sql_order;
			
			if ($sql)
			{
				if (!$sql_limit)
				{
					$result = $this->db->sql_query($sql);
				}
				else
				{
					$result = $this->db->sql_query_limit($sql, $sql_limit);
				}

				while ($row = $this->db->sql_fetchrow($result))
				{
					$id_ary[] = $row[$search_results . '_id'];
				}
				$this->db->sql_freeresult($result);

				$total_match_count = sizeof($id_ary);
				$id_ary = array_slice($id_ary, $start, $images_per_page);
			}

			$l_search_matches = $this->user->lang('FOUND_SEARCH_MATCHES', $total_match_count);

			// For some searches we need to print out the "no results" page directly to allow re-sorting/refining the search options.
			if (!sizeof($id_ary))
			{
				trigger_error('NO_SEARCH_RESULTS');
			}

			$sql_where = '';

			if (sizeof($id_ary))
			{
				$sql_where .= ($search_results == 'image') ? $this->db->sql_in_set('i.image_id', $id_ary) : $this->db->sql_in_set('c.comment_id', $id_ary);
			}

			// define some vars for urls
			$hilit = explode(' ', preg_replace('#\s+#u', ' ', str_replace(array('+', '-', '|', '(', ')', '&quot;'), ' ', $keywords)));
			$searchwords = implode(', ', $hilit);
			$hilit = implode('|', $hilit);
			// Do not allow *only* wildcard being used for hilight
			$hilit = (strspn($hilit, '*') === strlen($hilit)) ? '' : $hilit;

			$u_hilit = urlencode(htmlspecialchars_decode(str_replace('|', ' ', $hilit)));
			$u_search_album = implode('&amp;aid%5B%5D=', $search_album);

			$u_search = $this->url->append_sid('search', $u_sort_param);
			$u_search .= ($search_id) ? '&amp;search_id=' . $search_id : '';
			//@todo: 
			$u_search .= ($search_terms != 'all') ? '&amp;terms=' . $search_terms : '';
			$u_search .= ($u_hilit) ? '&amp;keywords=' . urlencode(htmlspecialchars_decode($keywords)) : '';
			$u_search .= ($username) ? '&amp;username=' . urlencode(htmlspecialchars_decode($username)) : '';
			$u_search .= ($user_id) ? '&amp;user_id=' . $user_id : '';
			$u_search .= ($u_search_album) ? '&amp;aid%5B%5D=' . $u_search_album : '';
			$u_search .= (!$search_child) ? '&amp;sc=0' : '';
			$u_search .= ($search_fields != 'all') ? '&amp;sf=' . $search_fields : '';

			//phpbb_generate_template_pagination($template, $u_search, 'pagination', 'start', $total_match_count, $images_per_page, $start);

			$this->template->assign_vars(array(
				'SEARCH_MATCHES'	=> $l_search_matches,
				//'PAGE_NUMBER'		=> phpbb_on_page($template, $user, $u_search, $total_match_count, $images_per_page, $start),

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
				'S_THUMBNAIL_SIZE'	=> $this->gallery_config->get('thumbnail_height') + 20 + (($this->gallery_config->get('thumbnail_infoline')) ? \phpbbgallery\core\image\image::THUMBNAIL_INFO_HEIGHT : 0),
			));
		}
		
		if ($sql_where)
		{
			// Search results are images
			if ($search_results == 'image')
			{
				$sql_array = array(
					'SELECT'		=> 'i.*, a.album_name, a.album_status, a.album_user_id',
					'FROM'			=> array($this->images_table => 'i'),

					'LEFT_JOIN'		=> array(
						array(
							'FROM'		=> array($this->albums_table => 'a'),
							'ON'		=> 'a.album_id = i.image_album_id',
						),
					),

					'WHERE'			=> 'i.image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN . ' AND ' . $sql_where,
					'ORDER_BY'		=> $sql_order,
				);
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$rowset = array();

				while ($row = $this->db->sql_fetchrow($result))
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
				$this->db->sql_freeresult($result);

				$columns_per_page = $this->gallery_config->get('album_columns'); //@todo: ($search_id == 'contests') ? phpbb_gallery_contest::NUM_IMAGES : $phpbb_ext_gallery->config->get('album_columns');
				$init_block = true;
				if ($search_id == 'contests')
				{
					foreach ($contest_images as $contest => $contest_data)
					{
						$num = 0;
						$this->template->assign_block_vars('imageblock', array(
							'U_BLOCK'			=> $this->url->append_sid('album', 'album_id=' . $contest_data['album_id'] . '&amp;sk=ra&amp;sd=d'),
							'BLOCK_NAME'		=> sprintf($this->user->lang['CONTEST_WINNERS_OF'], $contest_data['album_name']),
							'S_CONTEST_BLOCK'	=> true,
							'S_COL_WIDTH'		=> '33%',
							'S_COLS'			=> 3,
						));
					/*	foreach ($contest_data['images'] as $contest_image)
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
						}*/
					}
				}
				else
				{
					for ($i = 0, $end = count($rowset); $i < $end; $i += $columns_per_page)
					{
						if ($init_block)
						{
							$this->template->assign_block_vars('imageblock', array(
								'U_BLOCK'		=> $u_search,
								'BLOCK_NAME'	=> ($l_search_title) ? $l_search_title : $l_search_matches,
								'S_COL_WIDTH'	=> (100 / $this->gallery_config->get('album_columns')) . '%',
								'S_COLS'		=> $this->gallery_config->get('album_columns'),
							));
							$init_block = false;
						}
						$this->template->assign_block_vars('imageblock.imagerow', array());

						for ($j = $i, $end_columns = ($i + $columns_per_page); $j < $end_columns; $j++)
						{
							if ($j >= $end)
							{
								$this->template->assign_block_vars('imageblock.imagerow.noimage', array());
								continue;
							}

							// Assign the image to the template-block
							//$this->image->assign_block('imageblock.imagerow.image', $rowset[$j], $rowset[$j]['album_status'], $phpbb_ext_gallery->config->get('search_display'), $rowset[$j]['album_user_id']);
						}
					}
				}
			}
			// Search results are comments
			else
			{
				$sql_array = array(
					'SELECT'		=> 'c.*, i.*',
					'FROM'			=> array($this->comments_table => 'c'),

					'LEFT_JOIN'		=> array(
						array(
							'FROM'		=> array($this->images_table => 'i'),
							'ON'		=> 'c.comment_image_id = i.image_id',
						),
					),

					'WHERE'			=> $sql_where,
					'ORDER_BY'		=> $sql_order,
				);
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);

				while ($commentrow = $this->db->sql_fetchrow($result))
				{
					$image_id = $commentrow['image_id'];
					$album_id = $commentrow['image_album_id'];

					$this->template->assign_block_vars('commentrow', array(
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
				$this->db->sql_freeresult($result);

				$this->template->assign_vars(array(
					'DELETE_IMG'		=> $user->img('icon_post_delete', 'DELETE_COMMENT'),
					'EDIT_IMG'			=> $user->img('icon_post_edit', 'EDIT_COMMENT'),
					'QUOTE_IMG'			=> $user->img('icon_post_quote', 'QUOTE_COMMENT'),
					'INFO_IMG'			=> $user->img('icon_post_info', 'IP'),
					'MINI_POST_IMG'		=> $user->img('icon_post_target', 'COMMENT'),
					'PROFILE_IMG'		=> $user->img('icon_user_profile', 'READ_PROFILE'),
				));
			}
		}
		$s_albums = $this->album->get_albumbox(false, false, false, 'i_view' /*'a_search'*/);
		
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

		$this->template->assign_vars(array(
			'S_SEARCH_ACTION'		=> $this->helper->route('phpbbgallery_search'), // We force no ?sid= appending by using 0
			'S_HIDDEN_FIELDS'		=> build_hidden_fields($s_hidden_fields),
			'S_ALBUM_OPTIONS'		=> $s_albums,
			'S_SELECT_SORT_DIR'		=> $s_sort_dir,
			'S_SELECT_SORT_KEY'		=> $s_sort_key,
			'S_SELECT_SORT_DAYS'	=> $s_limit_days,
			'S_IN_SEARCH'			=> true,
		));
		return $this->helper->render('gallery/search_body.html', $this->user->lang('GALLERY'));
	}
	
	/**
	* Index Controller
	*	Route: gallery/search/random
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	
	public function random()
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('search');
		
		// Is user able to search? Has search been disabled?
		if (!$this->auth->acl_get('u_search') || !$this->config['load_search'])
		{
			$this->template->assign_var('S_NO_SEARCH', true);
			trigger_error('NO_SEARCH');
		}
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['GALLERY'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_search'),
		));	
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH_RANDOM'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_search_random'),
		));

		$this->gallery_search->random($this->gallery_config->get('items_per_page'));
		
		return $this->helper->render('gallery/search_random.html', $this->user->lang('GALLERY'));
	}
	
	/**
	* Index Controller
	*	Route: gallery/search/recent/{page}
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function recent($page)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('search');
		
		// Is user able to search? Has search been disabled?
		if (!$this->auth->acl_get('u_search') || !$this->config['load_search'])
		{
			$this->template->assign_var('S_NO_SEARCH', true);
			trigger_error('NO_SEARCH');
		}
		
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['GALLERY'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_search'),
		));	
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH_RECENT'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_search_recent'),
		));	
		
		$limit = $this->gallery_config->get('items_per_page');
		$start = ($page - 1) * $this->gallery_config->get('items_per_page');
		$image_counter = $this->gallery_search->recent_count();
		
		$this->gallery_search->recent($limit, $start);
		
		$this->template->assign_vars(array(
			'TOTAL_IMAGES'				=> $this->user->lang('VIEW_ALBUM_IMAGES', $image_counter),
		));
		
		$this->pagination->generate_template_pagination(array(
			'routes' => array(
				'phpbbgallery_search_recent',
				'phpbbgallery_search_recent_page',),
				'params' => array()), 'pagination', 'page', $image_counter, $limit, $start
		);
		return $this->helper->render('gallery/search_recent.html', $this->user->lang('GALLERY'));
	}
}