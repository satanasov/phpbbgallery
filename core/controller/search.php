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
		$search_id		= $this->request->variable('search_id', '');
		$image_id		= $this->request->variable('image_id', 0);

		$submit			= $this->request->variable('submit', false);
		$keywords		= utf8_normalize_nfc($this->request->variable('keywords', '', true));
		$add_keywords	= utf8_normalize_nfc($this->request->variable('add_keywords', '', true));
		$username		= $this->request->variable('username', '', true);
		$user_id		= $this->request->variable('user_id', array(0));
		$search_terms	= $this->request->variable('terms', 'all');
		$search_album	= $this->request->variable('aid', array(0));
		$search_child	= $this->request->variable('sc', true);
		$search_fields	= $this->request->variable('sf', 'all');
		$sort_days		= $this->request->variable('st', 0);
		$sort_key		= $this->request->variable('sk', 't');
		$sort_dir		= $this->request->variable('sd', 'd');

		$start 			= $this->request->variable('start', 0);
		$this->user->add_lang_ext('phpbbgallery/core', array('gallery'));
		$this->user->add_lang('search');
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

		// We will build SQL array and then build query (easely change count with rq)
		$sql_array = $sql_where = array();
		$sql_array['FROM'] = array(
			$this->images_table => 'i',
		);
		$sql_array['ORDER_BY'] = $sql_order;
		$sql_array['GROUP_BY'] = $sort_by_sql[$sort_key];
		if ($keywords || $username || $user_id || $search_id || $submit)
		{
			// Let's resolve username to user id ... or array of them.
			if ($username)
			{
				if ((strpos($username, '*') !== false) && (utf8_strlen(str_replace(array('*', '%'), '', $username)) < $this->config['min_search_author_chars']))
				{
					trigger_error(sprintf($this->user->lang['TOO_FEW_AUTHOR_CHARS'], $this->config['min_search_author_chars']));
				}
				$username_parsed = (strpos($username, '*') !== false) ? ' username_clean ' . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), utf8_clean_string($this->db->sql_escape($username)))) : ' username_clean = \'' . $this->db->sql_escape(utf8_clean_string($username)) .'\'';
				$sql = 'SELECT user_id
					FROM ' . USERS_TABLE . '
					WHERE ' . $username_parsed . '
					AND user_type IN (' . USER_NORMAL . ', ' . USER_FOUNDER . ')';
				$result = $this->db->sql_query_limit($sql, 100);

				while ($row = $this->db->sql_fetchrow($result))
				{
					$user_id_ary[] = (int) $row['user_id'];
				}
				$this->db->sql_freeresult($result);
				$user_id_ary[] = (int) ANONYMOUS;
				$user_id = $user_id_ary;
			}

			if (!empty($user_id))
			{
				$sql_where[] =  $this->db->sql_in_set('i.image_user_id', $user_id);
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
			$matches = array('i.image_name', 'i.image_desc');

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
			$sql_where[] = $search_query;

			if (empty($search_album))
			{
				$sql_where[] = $this->db->sql_in_set('i.image_album_id', $this->gallery_auth->acl_album_ids('i_view'));
			}
			else
			{
				$sql_where[] = $this->db->sql_in_set('i.image_album_id', $search_album);
			}
			$sql_array['WHERE'] = implode(' and ', array_filter($sql_where));
			$sql_array['SELECT'] = 'COUNT(i.image_id) as count';

			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$search_count = $row['count'];
			$this->db->sql_freeresult($result);
			if ($search_count == 0)
			{
				trigger_error('NO_SEARCH_RESULTS');
			}
			$sql_array['SELECT'] = '*';
			$sql_array['GROUP_BY']	= 'image_id';
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query_limit($sql, $this->gallery_config->get('items_per_page'), $start);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$rowset[] = $row;
			}
			$this->db->sql_freeresult($result);
			$this->template->assign_block_vars('imageblock', array(
				'BLOCK_NAME'	=> '',
				'U_BLOCK'	=> $this->helper->route('phpbbgallery_core_search'),
			));

			// Now let's get display options
			$show_ip = $show_ratings = $show_username = $show_views = $show_time = $show_imagename = $show_comments = $show_album = false;
			$show_options = $this->gallery_config->get('search_display');
			if ($show_options >= 128)
			{
				$show_ip = true;
				$show_options = $show_options - 128;
			}
			if ($show_options >= 64)
			{
				$show_ratings = true;
				$show_options = $show_options - 64;
			}
			if ($show_options >= 32)
			{
				$show_username = true;
				$show_options = $show_options - 32;
			}
			if ($show_options >= 16)
			{
				$show_views = true;
				$show_options = $show_options - 16;
			}
			if ($show_options >= 8)
			{
				$show_time = true;
				$show_options = $show_options - 8;
			}
			if ($show_options >= 4)
			{
				$show_imagename = true;
				$show_options = $show_options - 4;
			}
			if ($show_options >= 2)
			{
				$show_comments = true;
				$show_options = $show_options - 2;
			}
			if ($show_options == 1)
			{
				$show_album = true;
			}
			foreach ($rowset as $row)
			{
				switch ($this->gallery_config->get('link_thumbnail'))
				{
					case 'image_page':
						$action = $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id']));
					break;
					case 'image':
						$action = $this->helper->route('phpbbgallery_core_image_file_source', array('image_id' => $row['image_id']));
					break;
					default:
						$action = false;
					break;
				}
				switch ($this->gallery_config->get('link_image_name'))
				{
					case 'image_page':
						$action_image = $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id']));
					break;
					case 'image':
						$action_image = $this->helper->route('phpbbgallery_core_image_file_source', array('image_id' => $row['image_id']));
					break;
					default:
						$action_image = false;
					break;
				}
				$album_data = $this->album->get_info($row['image_album_id']);

				$this->template->assign_block_vars('imageblock.image', array(
					'IMAGE_ID'		=> $row['image_id'],
					'U_IMAGE'		=> $action_image,
					'UC_IMAGE_NAME'	=> $show_imagename ? $row['image_name'] : false,
					//'UC_THUMBNAIL'	=> 'self::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id']),
					'U_ALBUM'	=> $show_album ? $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_data['album_id'])) : false,
					'ALBUM_NAME'	=> $show_album ? $album_data['album_name'] : false,
					'IMAGE_VIEWS'	=> $show_views ? $row['image_view_count'] : -1,
					'UC_THUMBNAIL'		=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $row['image_id'])),
					'UC_THUMBNAIL_ACTION'	=> $action,
					'S_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED)) ? true : false,
					'S_LOCKED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED) ? true : false,
					'S_REPORTED'	=> ($this->gallery_auth->acl_check('m_report', $row['image_album_id'], $album_data['album_user_id']) && $row['image_reported']) ? true : false,
					'POSTER'		=> $show_username ? get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']) : false,
					'TIME'			=> $show_time ? $this->user->format_date($row['image_time']) : false,

					'S_RATINGS'		=> ($this->config['phpbb_gallery_allow_rates'] == 1 && $show_ratings) ? ($row['image_rates'] > 0 ? $row['image_rate_avg'] / 100 : $this->user->lang('NOT_RATED')) : false,
					'U_RATINGS'		=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id'])) . '#rating',
					'L_COMMENTS'	=> ($row['image_comments'] == 1) ? $this->user->lang['COMMENT'] : $this->user->lang['COMMENTS'],
					'S_COMMENTS'	=> ($this->config['phpbb_gallery_allow_comments'] && $this->gallery_auth->acl_check('c_read', $row['image_album_id'], $album_data['album_user_id']) && $show_comments) ? (($row['image_comments']) ? $row['image_comments'] : $this->user->lang['NO_COMMENTS']) : '',
					'U_COMMENTS'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id'])) . '#comments',

					'U_USER_IP'		=> $show_ip && $this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) ? $row['image_user_ip'] : false,
					'S_IMAGE_REPORTED'		=> $row['image_reported'],
					'U_IMAGE_REPORTED'		=> '',//($image_data['image_reported']) ? $phpbb_ext_gallery->url->append_sid('mcp', "mode=report_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_reported']) : '',
					'S_STATUS_APPROVED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_APPROVED),
					'S_STATUS_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && $row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED) ? true : false,
					'S_STATUS_UNAPPROVED_ACTION'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && $row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED) ? $this->helper->route('phpbbgallery_core_moderate_image_approve', array('image_id' => $row['image_id'])) : '',
					'S_STATUS_LOCKED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED),

					'U_REPORT'	=> ($this->gallery_auth->acl_check('m_report', $row['image_album_id'], $album_data['album_user_id']) && $row['image_reported']) ? '123'/*$this->url->append_sid('mcp', "mode=report_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_reported'])*/ : '',
					'U_STATUS'	=> '',//($this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id)) ? $phpbb_ext_gallery->url->append_sid('mcp', "mode=queue_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_id']) : '',
					'L_STATUS'	=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED) ? $this->user->lang['APPROVE_IMAGE'] : (($row['image_status'] == \phpbbgallery\core\image\image::STATUS_APPROVED) ? $this->user->lang['CHANGE_IMAGE_STATUS'] : $this->user->lang['UNLOCK_IMAGE']),
				));
			}
			$this->template->assign_vars(array(
				'SEARCH_MATCHES'	=> $this->user->lang('SEARCH'),
			));
			$url = '';
			if ($keywords != '')
			{
				$params[] = 'keywords=' . $keywords;
			}
			if ($username != '')
			{
				$params[] = 'username=' . $username;
			}
			if (!empty($user_id))
			{
				foreach ($user_id as $var)
				{
					$params[] = 'user_id[]=' . $var;
				}
			}
			if (isset($search_terms))
			{
				$params[] = 'terms=' . $search_terms;
			}
			if (!empty($search_album))
			{
				foreach ($search_album as $var)
				{
					$params[] = 'aid[]=' . $var;
				}
			}
			if (isset($search_child))
			{
				$params[] = 'sc=' . $search_child;
			}
			if (isset($search_fields))
			{
				$params[] = 'sf=' . $search_fields;
			}
			if (isset($sort_days))
			{
				$params[] = 'st=' . $sort_days;
			}
			if (isset($sort_key))
			{
				$params[] = 'sk=' . $sort_key;
			}
			if (isset($sort_dir))
			{
				$params[] = 'sd=' . $sort_dir;
			}
			$url = implode('&', $params);
			$this->pagination->generate_template_pagination(
				$this->helper->route('phpbbgallery_core_search') . '?' . $url,
				'pagination',
				'start',
				$search_count,
				$this->gallery_config->get('items_per_page'),
				$start
			);

			return $this->helper->render('gallery/search_results.html', $this->user->lang('GALLERY'));
		}
		// Is user able to search? Has search been disabled?
		if (!$this->auth->acl_get('u_search') || !$this->config['load_search'])
		{
			$this->template->assign_var('S_NO_SEARCH', true);
			trigger_error('NO_SEARCH');
		}
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search'),
		));

		$s_albums = $this->album->get_albumbox(false, false, false, 'i_view' /*'a_search'*/);
		$s_hidden_fields = array();
		$this->template->assign_vars(array(
			'S_SEARCH_ACTION'		=> $this->helper->route('phpbbgallery_core_search'), // We force no ?sid= appending by using 0
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
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH_RANDOM'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search_random'),
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
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH_RECENT'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search_recent'),
		));

		$limit = $this->gallery_config->get('items_per_page');
		$start = ($page - 1) * $this->gallery_config->get('items_per_page');
		$image_counter = $this->gallery_search->recent_count();

		$this->gallery_search->recent($limit, $start);

		return $this->helper->render('gallery/search_recent.html', $this->user->lang('GALLERY'));
	}
	/**
	* Index Controller
	*	Route: gallery/search/recent/{page}
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function recent_comments($page)
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
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH_RECENT_COMMENTS'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search_commented'),
		));

		$limit = $this->gallery_config->get('items_per_page');
		$start = ($page - 1) * $this->gallery_config->get('items_per_page');

		$this->gallery_search->recent_comments($limit, $start);

		return $this->helper->render('gallery/search_results.html', $this->user->lang('GALLERY'));
	}
	/**
	* Index Controller
	*	Route: gallery/search/self/{page}
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function ego_search($page)
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
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang('SEARCH_USER_IMAGES_OF', $this->user->data['username']),
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search_egosearch'),
		));

		$limit = $this->gallery_config->get('items_per_page');
		$start = ($page - 1) * $this->gallery_config->get('items_per_page');

		$this->gallery_search->recent($limit, $start, $this->user->data['user_id']);

		return $this->helper->render('gallery/search_results.html', $this->user->lang('GALLERY'));
	}
	/**
	* Index Controller
	*	Route: gallery/search/toprated/{page}
	*
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function toprated($page)
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
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->user->lang('SEARCH_TOPRATED'),
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_search_egosearch'),
		));

		$limit = $this->gallery_config->get('items_per_page');
		$start = ($page - 1) * $this->gallery_config->get('items_per_page');

		$this->gallery_search->rating($limit, $start);

		return $this->helper->render('gallery/search_results.html', $this->user->lang('GALLERY'));
	}
}
