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
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, 
	\phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbbgallery\core\album\display $display, \phpbbgallery\core\config $gallery_config, 
	\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\album\album $album, \phpbbgallery\core\image\image $image, \phpbbgallery\core\url $url,
	$images_table, $albums_table, $comments_table, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
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
		$this->images_table = $images_table;
		$this->albums_table = $albums_table;
		$this->comments_table = $comments_table;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Generate random images and populate template
	* @param (int)	$limit	how many images to generate_link
	*/

	public function random($limit)
	{
		// Define some vars
		$images_per_page = $limit;
		
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);

		switch ($this->db->get_sql_layer())
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
			FROM ' . $this->images_table . '
			WHERE image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN . '
				AND ((' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('i_view'), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR (' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('a_list'), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('m_status'), false, true) . ')
			ORDER BY ' . $sql_order;

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
			$id_ary[] = $row['image_id'];
		}
		$this->db->sql_freeresult($result);

		$total_match_count = sizeof($id_ary);

		$l_search_matches = $this->user->lang('FOUND_SEARCH_MATCHES', $total_match_count);
		
		// For some searches we need to print out the "no results" page directly to allow re-sorting/refining the search options.
		if (!sizeof($id_ary))
		{
			trigger_error('NO_SEARCH_RESULTS');
		}
		
		$sql_where = $this->db->sql_in_set('i.image_id', $id_ary);
		
		$this->template->assign_block_vars('random', array(
			'BLOCK_NAME'	=> $this->user->lang['RANDOM_IMAGES'],
			'U_BLOCK'	=> $this->helper->route('phpbbgallery_search_random'),
		));

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
		
		foreach ($rowset as $row)
		{
			$album_data = $this->album->get_info($row['image_album_id']);
			
			$this->template->assign_block_vars('random.image', array(
				'IMAGE_ID'		=> $row['image_id'],
				'U_IMAGE'		=> $this->helper->route('phpbbgallery_image', array('image_id' => $row['image_id'])),
				'UC_IMAGE_NAME'	=> $row['image_name'],//self::generate_link('image_name', $this->config['phpbb_gallery_link_image_name'], $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id'], false, true, "&amp;sk={$sk}&amp;sd={$sd}&amp;st={$st}"),
				//'UC_THUMBNAIL'	=> 'self::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id']),
				'UC_THUMBNAIL'		=> $this->helper->route('phpbbgallery_image_file_mini', array('image_id' => $row['image_id'])),
				'S_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED)) ? true : false,
				'S_LOCKED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED) ? true : false,
				'S_REPORTED'	=> ($this->gallery_auth->acl_check('m_report', $row['image_album_id'], $album_data['album_user_id']) && $row['image_reported']) ? true : false,
				'POSTER'		=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
				'TIME'			=> $this->user->format_date($row['image_time']),

				'S_RATINGS'		=> ($this->config['phpbb_gallery_allow_rates'] && $this->gallery_auth->acl_check('i_rate', $row['image_album_id'], $album_data['album_user_id'])) ? $row['image_rate_avg'] : '',
				'U_RATINGS'		=> $this->helper->route('phpbbgallery_image', array('image_id' => $row['image_id'])) . '#rating',
				'L_COMMENTS'	=> ($row['image_comments'] == 1) ? $this->user->lang['COMMENT'] : $this->user->lang['COMMENTS'],
				'S_COMMENTS'	=> ($this->config['phpbb_gallery_allow_comments'] && $this->gallery_auth->acl_check('c_read', $row['image_album_id'], $album_data['album_user_id'])) ? (($row['image_comments']) ? $row['image_comments'] : $this->user->lang['NO_COMMENTS']) : '',
				'U_COMMENTS'	=> $this->helper->route('phpbbgallery_image', array('image_id' => $row['image_id'])) . '#comments',

				'S_IMAGE_REPORTED'		=> $row['image_reported'],
				'U_IMAGE_REPORTED'		=> '',//($image_data['image_reported']) ? $phpbb_ext_gallery->url->append_sid('mcp', "mode=report_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_reported']) : '',
				'S_STATUS_APPROVED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_APPROVED),
				'S_STATUS_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && $row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED) ? true : false,
				'S_STATUS_UNAPPROVED_ACTION'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && $row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED) ? $this->helper->route('phpbbgallery_moderate_image_approve', array('image_id' => $row['image_id'])) : '',
				'S_STATUS_LOCKED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED),

				'U_REPORT'	=> ($this->gallery_auth->acl_check('m_report', $row['image_album_id'], $album_data['album_user_id']) && $row['image_reported']) ? '123'/*$this->url->append_sid('mcp', "mode=report_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_reported'])*/ : '',
				'U_STATUS'	=> '',//($this->auth->acl_check('m_status', $image_data['image_album_id'], $album_user_id)) ? $phpbb_ext_gallery->url->append_sid('mcp', "mode=queue_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_id']) : '',
				'L_STATUS'	=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED) ? $this->user->lang['APPROVE_IMAGE'] : (($row['image_status'] == \phpbbgallery\core\image\image::STATUS_APPROVED) ? $this->user->lang['CHANGE_IMAGE_STATUS'] : $this->user->lang['UNLOCK_IMAGE']),
			));
		}
	}
	
	/**
	* Generate recent images and populate template
	* @param (int)	$limit How many imagese to query
	* @param (int)	$start From which image to start
	*/

	public function recent($limit, $start = 0)
	{
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$sql_order = 'image_id DESC';
		$sql_limit = $limit;
		$sql = 'SELECT image_id
			FROM ' . $this->images_table . '
			WHERE image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN . '
				AND ((' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('i_view'), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('m_status'), false, true) . ')
			ORDER BY ' . $sql_order;
		if (!$sql_limit)
		{
			$result = $this->db->sql_query($sql);
		}
		else
		{
			$result = $this->db->sql_query_limit($sql, $sql_limit, $start);
		}
		//var_dump($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$id_ary[] = $row['image_id'];
		}

		$this->db->sql_freeresult($result);

		$total_match_count = sizeof($id_ary);

		$l_search_matches = $this->user->lang('FOUND_SEARCH_MATCHES', $total_match_count);
		
		// For some searches we need to print out the "no results" page directly to allow re-sorting/refining the search options.
		if (!sizeof($id_ary))
		{
			trigger_error('NO_SEARCH_RESULTS');
		}
		
		$sql_where = $this->db->sql_in_set('i.image_id', $id_ary);
		
		$this->template->assign_block_vars('recent', array(
			'BLOCK_NAME'	=> $this->user->lang['RECENT_IMAGES'],
			'U_BLOCK'	=> $this->helper->route('phpbbgallery_search_recent'),
		));

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
		
		foreach ($rowset as $row)
		{
			$album_data = $this->album->get_info($row['image_album_id']);
			
			$this->template->assign_block_vars('recent.image', array(
				'IMAGE_ID'		=> $row['image_id'],
				'U_IMAGE'		=> $this->helper->route('phpbbgallery_image', array('image_id' => $row['image_id'])),
				'UC_IMAGE_NAME'	=> $row['image_name'],//self::generate_link('image_name', $this->config['phpbb_gallery_link_image_name'], $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id'], false, true, "&amp;sk={$sk}&amp;sd={$sd}&amp;st={$st}"),
				//'UC_THUMBNAIL'	=> 'self::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $image_data['image_id'], $image_data['image_name'], $image_data['image_album_id']),
				'UC_THUMBNAIL'		=> $this->helper->route('phpbbgallery_image_file_mini', array('image_id' => $row['image_id'])),
				'S_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED)) ? true : false,
				'S_LOCKED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_LOCKED) ? true : false,
				'S_REPORTED'	=> ($this->gallery_auth->acl_check('m_report', $row['image_album_id'], $album_data['album_user_id']) && $row['image_reported']) ? true : false,
				'POSTER'		=> get_username_string('full', $row['image_user_id'], $row['image_username'], $row['image_user_colour']),
				'TIME'			=> $this->user->format_date($row['image_time']),

				'S_RATINGS'		=> ($this->config['phpbb_gallery_allow_rates'] && $this->gallery_auth->acl_check('i_rate', $row['image_album_id'], $album_data['album_user_id'])) ? $row['image_rate_avg'] : '',
				'U_RATINGS'		=> $this->helper->route('phpbbgallery_image', array('image_id' => $row['image_id'])) . '#rating',
				'L_COMMENTS'	=> ($row['image_comments'] == 1) ? $this->user->lang['COMMENT'] : $this->user->lang['COMMENTS'],
				'S_COMMENTS'	=> ($this->config['phpbb_gallery_allow_comments'] && $this->gallery_auth->acl_check('c_read', $row['image_album_id'], $album_data['album_user_id'])) ? (($row['image_comments']) ? $row['image_comments'] : $this->user->lang['NO_COMMENTS']) : '',
				'U_COMMENTS'	=> $this->helper->route('phpbbgallery_image', array('image_id' => $row['image_id'])) . '#comments',

				'S_IMAGE_REPORTED'		=> $row['image_reported'],
				'U_IMAGE_REPORTED'		=> '',//($image_data['image_reported']) ? $phpbb_ext_gallery->url->append_sid('mcp', "mode=report_details&amp;album_id={$image_data['image_album_id']}&amp;option_id=" . $image_data['image_reported']) : '',
				'S_STATUS_APPROVED'		=> ($row['image_status'] == \phpbbgallery\core\image\image::STATUS_APPROVED),
				'S_STATUS_UNAPPROVED'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && $row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED) ? true : false,
				'S_STATUS_UNAPPROVED_ACTION'	=> ($this->gallery_auth->acl_check('m_status', $row['image_album_id'], $album_data['album_user_id']) && $row['image_status'] == \phpbbgallery\core\image\image::STATUS_UNAPPROVED) ? $this->helper->route('phpbbgallery_moderate_image_approve', array('image_id' => $row['image_id'])) : '',
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
		$sql_order = 'image_id DESC';
		$sql = 'SELECT COUNT(image_id) as count
			FROM ' . $this->images_table . '
			WHERE image_status <> ' . \phpbbgallery\core\image\image::STATUS_ORPHAN . '
				AND ((' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('i_view'), false, true) . ' AND image_status <> ' . \phpbbgallery\core\image\image::STATUS_UNAPPROVED . ')
					OR ' . $this->db->sql_in_set('image_album_id', $this->gallery_auth->acl_album_ids('m_status'), false, true) . ')
			ORDER BY ' . $sql_order;

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		return (int) $row['count'];
	}
}