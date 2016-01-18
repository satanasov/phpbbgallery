<?php
/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 Luifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core;

class search
{
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
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper,
	\phpbbgallery\core\config $gallery_config, 	\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\album\album $album,
	\phpbbgallery\core\image\image $image, \phpbb\pagination $pagination, \phpbb\user_loader $user_loader,
	$images_table, $albums_table, $comments_table)
	{
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->gallery_config = $gallery_config;
		$this->gallery_auth = $gallery_auth;
		$this->album = $album;
		$this->image = $image;
		$this->pagination = $pagination;
		$this->user_loader = $user_loader;
		$this->images_table = $images_table;
		$this->albums_table = $albums_table;
		$this->comments_table = $comments_table;
	}

	/**
	* Generate random images and populate template
	* @param (int)	$limit	how many images to generate_link
	*/

	public function random($limit, $user = 0, $fields = 'rrc_gindex_display', $block_name = false, $u_block = false)
	{
		// Define some vars
		$images_per_page = $limit;

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);

		switch ($this->db->get_sql_layer())
		{
			case 'postgres':
			case 'sqlite3':
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
			FROM ' . $this->images_table . '
			WHERE image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN;
		if ($user > 0)
		{
			$sql .= ' and image_user_id = ' . (int) $user;
		}
		$exclude_albums = array();
		if (!$this->gallery_config->get('rrc_gindex_pegas'))
		{
			$sql_no_user = 'SELECT album_id FROM ' . $this->albums_table . ' WHERE album_user_id > 0';
			$result = $this->db->sql_query($sql_no_user);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$exclude_albums[] = (int) $row['album_id'];
			}
			$this->db->sql_freeresult($result);
		}
		$exclude_albums = array_merge($exclude_albums, $this->gallery_auth->get_exclude_zebra());
		$sql .= ' AND ((' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('i_view'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR (' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('a_list'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('m_status'), $exclude_albums), false, true) . ')
			ORDER BY ' . $sql_order;

		if (!$sql_limit)
		{
			$result = $this->db->sql_query($sql);
		}
		else
		{
			$result = $this->db->sql_query_limit($sql, $sql_limit);
		}
		$id_ary = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$id_ary[] = $row['image_id'];
		}
		$this->db->sql_freeresult($result);

		$total_match_count = sizeof($id_ary);

		$l_search_matches = $this->user->lang('FOUND_SEARCH_MATCHES', $total_match_count);

		$this->template->assign_block_vars('imageblock', array(
			'BLOCK_NAME'	=> $block_name ? $block_name : $this->user->lang['RANDOM_IMAGES'],
			'U_BLOCK'	=> $u_block ? $u_block : $this->helper->route('phpbbgallery_core_search_random'),
		));

		// For some searches we need to print out the "no results" page directly to allow re-sorting/refining the search options.
		if (!sizeof($id_ary))
		{
			$this->template->assign_block_vars('imageblock', array(
				'ERROR'	=> $this->user->lang('NO_SEARCH_RESULTS_RANDOM'),
			));
			return;
		}

		$sql_where = $this->db->sql_in_set('i.image_id', $id_ary);

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
			'GROUP_BY'	=> 'i.image_id, a.album_name, a.album_status, a.album_user_id',
			'ORDER_BY'		=> $sql_order,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rowset = array();

		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[] = $row;
		}
		$this->db->sql_freeresult($result);

		// Now let's get display options
		$show_ip = $show_ratings = $show_username = $show_views = $show_time = $show_imagename = $show_comments = $show_album = false;
		$show_options = $this->gallery_config->get($fields);
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
			$album_data = $this->album->get_info($row['image_album_id']);
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

			$this->template->assign_block_vars('imageblock.image', array(
				'IMAGE_ID'		=> $row['image_id'],
				'U_IMAGE'		=> $show_imagename ? $action_image : false,
				'UC_IMAGE_NAME'	=> $show_imagename ? $row['image_name'] : false,
				'U_ALBUM'	=> $show_album ? $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_data['album_id'])) : false,
				'ALBUM_NAME'	=> $show_album ? $album_data['album_name'] : false,
				'IMAGE_VIEWS'	=> $show_views ? $row['image_view_count'] : -1,
				//'UC_THUMBNAIL'	=> 'self::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id']),
				'UC_THUMBNAIL'		=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $row['image_id'])),
				'UC_THUMBNAIL_ACTION'	=> $action,
				'S_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED)) ? true : false,
				'S_LOCKED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED) ? true : false,
				'S_REPORTED'	=> ($this->gallery_auth->acl_check('m_report', $row['image_album_id'], $album_data['album_user_id']) && $row['image_reported']) ? true : false,
				'POSTER'		=> $show_username ? get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']) : false,
				'TIME'			=> $show_time ? $this->user->format_date($row['image_time']) : false,

				'S_RATINGS'		=> ($this->gallery_config->get('allow_rates') == 1 && $show_ratings) ? ($row['image_rates'] > 0 ? $row['image_rate_avg'] / 100 : $this->user->lang('NOT_RATED')) : false,
				'U_RATINGS'		=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id'])) . '#rating',
				'L_COMMENTS'	=> ($row['image_comments'] == 1) ? $this->user->lang['COMMENT'] : $this->user->lang['COMMENTS'],
				'S_COMMENTS'	=> $show_comments ? (($this->gallery_config->get('allow_comments') && $this->gallery_auth->acl_check('c_read', $row['image_album_id'], $album_data['album_user_id'])) ? (($row['image_comments']) ? $row['image_comments'] : $this->user->lang['NO_COMMENTS']) : '') : false,
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
	}

	/**
	* Get all recent images the user has access to
	* return (int) $images_count
	*/

	public function recent_count()
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);

		$sql = 'SELECT COUNT(image_id) as count
			FROM ' . $this->images_table . '
			WHERE image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN;
		$exclude_albums = array();
		if (!$this->gallery_config->get('rrc_gindex_pegas'))
		{
			$sql_no_user = 'SELECT album_id FROM ' . $this->albums_table . ' WHERE album_user_id > 0';
			$result = $this->db->sql_query($sql_no_user);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$exclude_albums[] = (int) $row['album_id'];
			}
			$this->db->sql_freeresult($result);
		}
		$exclude_albums = array_merge($exclude_albums, $this->gallery_auth->get_exclude_zebra());
		$sql .= '	AND ((' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('i_view'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('m_status'), $exclude_albums), false, true) . ')';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return (int) $row['count'];
	}

	/**
	* recent comments
	* @param (int)	$limit How many imagese to query
	* @param (int)	$start From which image to start
	*/

	public function recent_comments($limit, $start = 0)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$sql_limit = $limit;
		$sql_array = array(
			'FROM' => array(
				$this->images_table => 'i',
				$this->comments_table => 'c',
			),
			'WHERE'	=> 'i.image_id = c.comment_image_id and ' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('c_read'), false, true),
			'GROUP_BY'	=> 'c.comment_id, i.image_id',
			'ORDER_BY'	=> 'comment_time DESC'
		);
		$sql_array['SELECT'] = 'COUNT(c.comment_id) as count';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];
		$sql_array['SELECT'] = '*';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $sql_limit, $start);
		$rowset = array();

		$users_array = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[] = $row;
			$users_array[$row['comment_user_id']] = array('');
			$users_array[$row['image_user_id']] = array('');
		}
		$this->db->sql_freeresult($result);
		if (empty($rowset))
		{
			$this->template->assign_vars(array(
				'ERROR'	=> $this->user->lang('NO_SEARCH_RESULTS_RECENT_COMMENTS'),
			));
			return;
		}

		$this->user_loader->load_users(array_keys($users_array));
		foreach ($rowset as $var)
		{

			$album_tmp = $this->album->get_info($var['image_album_id']);
			$this->template->assign_block_vars('commentrow', array(
				'COMMENT_ID'	=> $var['comment_id'],
				'U_DELETE'	=> ($this->gallery_auth->acl_check('m_comments', $album_tmp['album_id'], $album_tmp['album_user_id']) || ($this->gallery_auth->acl_check('c_delete', $album_tmp['album_id'], $album_tmp['album_user_id']) && ($var['comment_user_id'] == $this->user->data['user_id']) && $this->user->data['is_registered'])) ? $this->helper->route('phpbbgallery_core_comment_delete', array('image_id' => $var['comment_image_id'], 'comment_id' => $var['comment_id'])) : false,
				'U_EDIT'	=> $this->gallery_auth->acl_check('c_edit', $album_tmp['album_id'], $album_tmp['album_user_id'])? $this->helper->route('phpbbgallery_core_comment_edit', array('image_id'	=> $var['comment_image_id'], 'comment_id'	=> $var['comment_id'])) : false,
				'U_QUOTE'	=> ($this->gallery_auth->acl_check('c_post', $album_tmp['album_id'], $album_tmp['album_user_id'])) ? $this->helper->route('phpbbgallery_core_comment_add', array('image_id'	=> $var['comment_image_id'], 'comment_id'	=> $var['comment_id'])) : false,
				'U_COMMENT'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $var['comment_image_id'])) . '#comment_' . $var['comment_id'],
				'POST_AUTHOR_FULL'	=> $this->user_loader->get_username($var['comment_user_id'], 'full'),
				'TIME'	=> $this->user->format_date($var['comment_time']),
				'TEXT'	=> generate_text_for_display($var['comment'], $var['comment_uid'], $var['comment_bitfield'], 7),
				'UC_IMAGE_NAME'	=> '<a href="' . $this->helper->route('phpbbgallery_core_image', array('image_id' => $var['comment_image_id'])) . '">' . $var['image_name'] . '</a>',
				// 'UC_THUMBNAIL'		=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $var['image_id'])),
				'UC_THUMBNAIL'		=> $this->image->generate_link('thumbnail', $this->gallery_config->get('link_thumbnail'), $var['comment_image_id'], $var['image_name'], $var['image_album_id']),
				'IMAGE_AUTHOR'		=> $this->user_loader->get_username((int) $var['image_user_id'], 'full'),
				'IMAGE_TIME'		=> $this->user->format_date($var['image_time']),
			));
		}
		$this->template->assign_vars(array(
			'SEARCH_MATCHES'	=> $this->user->lang('TOTAL_COMMENTS_SPRINTF', $count),
			'SEARCH_TITLE'		=> $this->user->lang('RECENT_COMMENTS'),
		));
		$this->pagination->generate_template_pagination(array(
			'routes' => array(
				'phpbbgallery_core_search_recent',
				'phpbbgallery_core_search_recent_page',),
				'params' => array()), 'pagination', 'page', $count, $limit, $start
		);
	}
	/**
	* Generate recent images and populate template
	* @param (int)	$limit How many imagese to query
	* @param (int)	$start From which image to start
	*/

	public function recent($limit, $start = 0, $user = 0, $fields = 'rrc_gindex_display', $block_name = false, $u_block = false)
	{
		$pagination = true;
		if ($start == -1)
		{
			$start = 0;
			$pagination = false;
		}
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$sql_order = 'image_id DESC';
		$sql_limit = $limit;
		$sql = 'SELECT COUNT(image_id) as count
			FROM ' . $this->images_table . '
			WHERE image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN;
		if ($user > 0)
		{
			$sql .= ' and image_user_id = ' . (int) $user;
		}
		$exclude_albums = array();
		if (!$this->gallery_config->get('rrc_gindex_pegas'))
		{
			$sql_no_user = 'SELECT album_id FROM ' . $this->albums_table . ' WHERE album_user_id > 0';
			$result = $this->db->sql_query($sql_no_user);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$exclude_albums[] = (int) $row['album_id'];
			}
			$this->db->sql_freeresult($result);
		}
		$exclude_albums = array_merge($exclude_albums, $this->gallery_auth->get_exclude_zebra());
		$sql .= ' AND ((' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('i_view'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('m_status'), $exclude_albums), false, true) . ')';

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];
		$sql = 'SELECT image_id
			FROM ' . $this->images_table . '
			WHERE image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN;
		if ($user > 0)
		{
			$sql .= ' and image_user_id = ' . (int) $user;
		}
		$sql .= ' AND ((' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('i_view'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('m_status'), $exclude_albums), false, true) . ')
			ORDER BY ' . $sql_order;

		$result = $this->db->sql_query_limit($sql, $sql_limit, $start);
		$id_ary = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$id_ary[] = $row['image_id'];
		}

		$this->db->sql_freeresult($result);

		$total_match_count = sizeof($id_ary);

		$l_search_matches = $this->user->lang('FOUND_SEARCH_MATCHES', $total_match_count);

		if ($user > 0)
		{
			$this->template->assign_block_vars('imageblock', array(
				'BLOCK_NAME'	=> $block_name ? $block_name : '' ,
				'U_BLOCK'	=> $u_block ? $u_block : $this->helper->route('phpbbgallery_core_search_egosearch'),
			));
		}
		else
		{
			$this->template->assign_block_vars('imageblock', array(
				'BLOCK_NAME'	=>  $block_name ? $block_name : $this->user->lang['RECENT_IMAGES'],
				'U_BLOCK'	=> $u_block ? $u_block : $this->helper->route('phpbbgallery_core_search_recent'),
			));
		}

		// For some searches we need to print out the "no results" page directly to allow re-sorting/refining the search options.
		if (!sizeof($id_ary))
		{
			$this->template->assign_block_vars('imageblock', array(
				'ERROR'	=> $this->user->lang('NO_SEARCH_RESULTS_RECENT')
			));
			return;
		}

		$sql_where = $this->db->sql_in_set('i.image_id', $id_ary);

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
			$rowset[] = $row;
		}
		$this->db->sql_freeresult($result);

		// Now let's get display options
		$show_ip = $show_ratings = $show_username = $show_views = $show_time = $show_imagename = $show_comments = $show_album = false;
		$show_options = $this->gallery_config->get($fields);

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
			$album_data = $this->album->get_info($row['image_album_id']);
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
			$this->template->assign_block_vars('imageblock.image', array(
				'IMAGE_ID'		=> $row['image_id'],
				'U_IMAGE'		=> $show_imagename ? $action_image : false,
				'UC_IMAGE_NAME'	=> $show_imagename ? $row['image_name'] : false,
				'U_ALBUM'	=> $show_album ? $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_data['album_id'])) : false,
				'ALBUM_NAME'	=> $show_album ? $album_data['album_name'] : false,
				'IMAGE_VIEWS'	=> $show_views ? $row['image_view_count'] : -1,
				//'UC_THUMBNAIL'	=> 'self::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id']),
				'UC_THUMBNAIL'		=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $row['image_id'])),
				'UC_THUMBNAIL_ACTION'	=> $action,
				'S_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED)) ? true : false,
				'S_LOCKED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED) ? true : false,
				'S_REPORTED'	=> ($this->gallery_auth->acl_check('m_report', $row['image_album_id'], $album_data['album_user_id']) && $row['image_reported']) ? true : false,
				'POSTER'		=> $show_username ? get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']) : false,
				'TIME'			=> $show_time ? $this->user->format_date($row['image_time']) : false,

				'S_RATINGS'		=> ($this->gallery_config->get('allow_rates') == 1 && $show_ratings) ? ($row['image_rates'] > 0 ? $row['image_rate_avg'] / 100 : $this->user->lang('NOT_RATED')) : false,
				'U_RATINGS'		=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id'])) . '#rating',
				'L_COMMENTS'	=> ($row['image_comments'] == 1) ? $this->user->lang['COMMENT'] : $this->user->lang['COMMENTS'],
				'S_COMMENTS'	=> $show_comments ? (($this->gallery_config->get('allow_comments') && $this->gallery_auth->acl_check('c_read', $row['image_album_id'], $album_data['album_user_id'])) ? (($row['image_comments']) ? $row['image_comments'] : $this->user->lang['NO_COMMENTS']) : '') : false,
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
		if ($user > 0)
		{
			$this->template->assign_vars(array(
				'SEARCH_MATCHES'	=> $this->user->lang('TOTAL_IMAGES_SPRINTF', $count),
				'SEARCH_TITLE'		=> $this->user->lang('SEARCH_USER_IMAGES_OF', $this->user->data['username']),
			));
			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_core_search_egosearch',
					'phpbbgallery_core_search_egosearch_page',),
					'params' => array()), 'pagination', 'page', $count, $limit, $start
			);
		}
		else
		{
			$this->template->assign_vars(array(
				'TOTAL_IMAGES'				=> $this->user->lang('VIEW_ALBUM_IMAGES', $count),
			));
			if ($pagination)
			{
				$this->pagination->generate_template_pagination(array(
					'routes' => array(
						'phpbbgallery_core_search_recent',
						'phpbbgallery_core_search_recent_page',),
						'params' => array()), 'pagination', 'page', $count, $limit, $start
				);
			}
		}
	}

	/**
	* Get top rated image
	*/
	public function rating($limit, $start = 0)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$sql_array = array();
		$sql_array['FROM'] = array(
			$this->images_table	=> 'i'
		);
		$sql_array['WHERE'] = $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('i_view'), false, true) . ' and image_rate_avg <> 0';
		$sql_array['ORDER_BY'] = 'image_rate_avg DESC, image_rates DESC';
		$sql_array['SELECT'] = 'COUNT(i.image_id) as count';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];
		$sql_array['SELECT'] = '*';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $limit, $start);
		$rowset = array();

		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[] = $row;
			$users_array[$row['image_user_id']] = array('');
		}
		$this->db->sql_freeresult($result);
		if (empty($rowset))
		{
			$this->template->assign_var('S_NO_SEARCH', true);
			trigger_error('NO_SEARCH');
		}

		$this->template->assign_block_vars('imageblock', array(
			'BLOCK_NAME'	=> $this->user->lang['SEARCH_TOPRATED'],
			'U_BLOCK'	=> $this->helper->route('phpbbgallery_core_search_toprated'),
		));
		$this->user_loader->load_users(array_keys($users_array));
		// Now let's get display options
		$show_ip = $show_ratings = $show_username = $show_views = $show_time = $show_imagename = $show_comments = $show_album = false;
		$show_options = $this->gallery_config->get('rrc_gindex_display');
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
			$album_data = $this->album->get_info($row['image_album_id']);
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
			$this->template->assign_block_vars('imageblock.image', array(
				'IMAGE_ID'		=> $row['image_id'],
				'U_IMAGE'		=> $action_image,
				'UC_IMAGE_NAME'	=> $show_imagename ? $row['image_name'] : false,
				//'UC_THUMBNAIL'	=> 'self::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id']),
				'UC_THUMBNAIL'		=> $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $row['image_id'])),
				'UC_THUMBNAIL_ACTION'	=> $action,
				'S_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED)) ? true : false,
				'S_LOCKED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED) ? true : false,
				'S_REPORTED'	=> ($this->gallery_auth->acl_check('m_report', $row['image_album_id'], $album_data['album_user_id']) && $row['image_reported']) ? true : false,
				'POSTER'		=> $show_username ? get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']) : false,
				'TIME'			=> $show_time ? $this->user->format_date($row['image_time']) : false,

				'S_RATINGS'		=> ($show_ratings && $this->gallery_config->get('allow_rates') && $this->gallery_auth->acl_check('i_rate', $row['image_album_id'], $album_data['album_user_id'])) ? $row['image_rate_avg'] / 100 : '',
				'U_RATINGS'		=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id'])) . '#rating',
				'L_COMMENTS'	=> ($row['image_comments'] == 1) ? $this->user->lang['COMMENT'] : $this->user->lang['COMMENTS'],
				'S_COMMENTS'	=> $show_comments ? (($this->gallery_config->get('allow_comments') && $this->gallery_auth->acl_check('c_read', $row['image_album_id'], $album_data['album_user_id'])) ? (($row['image_comments']) ? $row['image_comments'] : $this->user->lang['NO_COMMENTS']) : '') : false,
				'U_COMMENTS'	=> $this->helper->route('phpbbgallery_core_image', array('image_id' => $row['image_id'])) . '#comments',

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
			'SEARCH_MATCHES'	=> $this->user->lang('TOTAL_IMAGES_SPRINTF', $count),
			'SEARCH_TITLE'		=> $this->user->lang('SEARCH_TOPRATED'),
		));
		$this->pagination->generate_template_pagination(array(
			'routes' => array(
				'phpbbgallery_core_search_toprated',
				'phpbbgallery_core_search_toprated_page',),
				'params' => array()), 'pagination', 'page', $count, $limit, $start
		);
	}
}
