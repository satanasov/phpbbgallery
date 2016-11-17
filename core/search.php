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
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db Database object
	 * @param \phpbb\template\template $template Template object
	 * @param \phpbb\user $user User object
	 * @param \phpbb\controller\helper $helper Controller helper object
	 * @param config $gallery_config
	 * @param auth\auth $gallery_auth
	 * @param album\album $album
	 * @param image\image $image
	 * @param \phpbb\pagination $pagination
	 * @param \phpbb\user_loader $user_loader
	 * @param $images_table
	 * @param $albums_table
	 * @param $comments_table
	 * @internal param \phpbb\auth\auth $auth Auth object
	 * @internal param \phpbb\config\config $config Config object
	 * @internal param \phpbb\request\request $request Request object
	 * @internal param album\display $display Albums display object
	 * @internal param string $root_path Root path
	 * @internal param string $php_ext php file extension
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
	 * @param (int)    $limit    how many images to generate_link
	 * @param int $user
	 * @param string $fields
	 * @param bool $block_name
	 * @param bool $u_block
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
			WHERE image_status <> ' . \phpbbgallery\core\block::STATUS_ORPHAN;
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
		$sql .= ' AND ((' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('i_view'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\block::STATUS_UNAPPROVED . ')
					OR (' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('a_list'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\block::STATUS_UNAPPROVED . ')
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
			'SELECT'		=> 'i.*, a.album_name, a.album_status, a.album_user_id, album_id',
			'FROM'			=> array($this->images_table => 'i'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array($this->albums_table => 'a'),
					'ON'		=> 'a.album_id = i.image_album_id',
				),
			),

			'WHERE'			=> 'i.image_status <> ' . \phpbbgallery\core\block::STATUS_ORPHAN . ' AND ' . $sql_where,
			'GROUP_BY'	=> 'i.image_id, a.album_name, a.album_status, a.album_user_id, a.album_id',
			'ORDER_BY'		=> $sql_order,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rowset = array();

		$show_options = $this->gallery_config->get($fields);
		$thumbnail_link = $this->gallery_config->get('link_thumbnail');
		$imagename_link = $this->gallery_config->get('link_image_name');

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->image->assign_block('imageblock.image', $row, $show_options, $thumbnail_link, $imagename_link);
		}

		$this->db->sql_freeresult($result);
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
			WHERE image_status <> ' . \phpbbgallery\core\block::STATUS_ORPHAN;
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
		$sql .= '	AND ((' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('i_view'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\block::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('m_status'), $exclude_albums), false, true) . ')';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return (int) $row['count'];
	}

	/**
	 * recent comments
	 * @param (int)    $limit How many imagese to query
	 * @param int $start
	 */
	public function recent_comments($limit, $start = 0)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$sql_limit = $limit;
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
		$sql_array = array(
			'FROM' => array(
				$this->images_table => 'i',
				$this->comments_table => 'c',
			),
			'WHERE'	=> 'i.image_id = c.comment_image_id and ' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('c_read'), false, true),
			'GROUP_BY'	=> 'c.comment_id, i.image_id',
			'ORDER_BY'	=> 'comment_time DESC'
		);
		$sql_array['WHERE'] .= ' AND ((' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('i_view'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\block::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('m_status'), $exclude_albums), false, true) . ')';

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
	 * @param (int)    $limit How many imagese to query
	 * @param int $start
	 * @param int $user
	 * @param string $fields
	 * @param bool $block_name
	 * @param bool $u_block
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
		$sql_order = '';
		switch ($this->gallery_config->get('default_sort_key'))
		{
			case 't':
				$sql_order = 'image_time';
				break;
			case 'n':
				$sql_order = 'image_name_clean';
				break;
			case 'vc':
				$sql_order = 'image_view_count';
				break;
			case 'u':
				$sql_order = 'image_username_clean';
				break;
			case 'ra':
				$sql_order = 'image_rate_avg';
				break;
			case 'r':
				$sql_order = 'image_rates';
				break;
			case 'c':
				$sql_order = 'image_comments';
				break;
			case 'lc':
				$sql_order = 'image_last_comment';
				break;
		}
		$sql_order = $sql_order . ($this->gallery_config->get('default_sort_dir') == 'd' ? ' DESC' : ' ASC');
		$sql_limit = $limit;
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
		$sql_ary = array(
			'FROM'	=>	array(
				$this->images_table	=> 'i'
			),
			'WHERE'	=> 'image_status <> ' . \phpbbgallery\core\block::STATUS_ORPHAN
		);
		if ($user > 0)
		{
			$sql_ary['WHERE'] .= ' and image_user_id = ' . (int) $user;
		}
		$sql_ary['WHERE'] .= ' AND ((' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('i_view'), $exclude_albums), false, true) . ' AND image_status <> ' . \phpbbgallery\core\block::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', array_diff($this->gallery_auth->acl_album_ids('m_status'), $exclude_albums), false, true) . ')';

		$sql_ary['SELECT'] = 'COUNT(image_id) as count';
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];

		$sql_ary['SELECT'] = 'image_id';
		$sql_ary['ORDER_BY'] = $sql_order;
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
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
			'SELECT'		=> 'i.*, a.album_name, a.album_status, a.album_user_id, a.album_id',
			'FROM'			=> array($this->images_table => 'i'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array($this->albums_table => 'a'),
					'ON'		=> 'a.album_id = i.image_album_id',
				),
			),

			'WHERE'			=> 'i.image_status <> ' . \phpbbgallery\core\block::STATUS_ORPHAN . ' AND ' . $sql_where,
			'ORDER_BY'		=> $sql_order,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);

		$show_options = $this->gallery_config->get($fields);
		$thumbnail_link = $this->gallery_config->get('link_thumbnail');
		$imagename_link = $this->gallery_config->get('link_image_name');

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->image->assign_block('imageblock.image', $row, $show_options, $thumbnail_link, $imagename_link);
		}
		$this->db->sql_freeresult($result);

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
	 * @param $limit
	 * @param int $start
	 */
	public function rating($limit, $start = 0)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$sql_array = array();
		$sql_array['FROM'] = array(
			$this->images_table	=> 'i'
		);
		$sql_array['WHERE'] = $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('i_view'), false, true) . ' and image_rate_avg <> 0';
		$sql_array['SELECT'] = 'COUNT(image_id) as count';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];
		$sql_array['SELECT'] = '* , a.album_name, a.album_status, a.album_user_id, a.album_id';
		$sql_array['LEFT_JOIN']	= array(
			array(
				'FROM'		=> array($this->albums_table => 'a'),
				'ON'		=> 'a.album_id = i.image_album_id',
			)
		);
		$sql_array['ORDER_BY'] = 'image_rate_avg DESC, image_rates DESC';
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
		$show_options = $this->gallery_config->get('rrc_gindex_display');
		$thumbnail_link = $this->gallery_config->get('link_thumbnail');
		$imagename_link = $this->gallery_config->get('link_image_name');
		foreach ($rowset as $row)
		{
			$this->image->assign_block('imageblock.image', $row, $show_options, $thumbnail_link, $imagename_link);
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
