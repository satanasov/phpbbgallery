<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\album;

class display
{
	protected $auth;
	protected $config;
	protected $db;
	protected $helper;
	protected $pagination;
	protected $request;
	protected $template;
	protected $user;
	protected $gallery_auth;
	protected $gallery_user;
	protected $root_path;
	protected $php_ext;
	protected $table_albums;
	protected $table_contests;
	protected $table_moderators;
	protected $table_tracking;
	public $album_start;
	public $album_limit;
	public $albums_total;
	public $album_mode;

	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\db\driver\driver_interface $db,
	\phpbb\pagination $pagination, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbbgallery\core\auth\auth $gallery_auth,
	\phpbbgallery\core\user $gallery_user, \phpbbgallery\core\misc $misc, $root_path, $php_ext, $albums_table, $contests_table, $tracking_table, $moderators_table)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->helper = $helper;
		$this->db = $db;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->gallery_auth = $gallery_auth;
		$this->gallery_user = $gallery_user;
		$this->misc = $misc;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_albums = $albums_table;
		$this->table_contests = $contests_table;
		$this->table_tracking = $tracking_table;
		$this->table_moderators = $moderators_table;
	}

	/**
	 * Get album branch
	 *
	 * borrowed from phpBB3
	 * @author: phpBB Group
	 * @function: get_forum_branch
	 * @param $branch_user_id
	 * @param $album_id
	 * @param string $type
	 * @param string $order
	 * @param bool $include_album
	 * @return array
	 */
	public function get_branch($branch_user_id, $album_id, $type = 'all', $order = 'descending', $include_album = true)
	{
		switch ($type)
		{
			case 'parents':
				$condition = 'a1.left_id BETWEEN a2.left_id AND a2.right_id';
			break;

			case 'children':
				$condition = 'a2.left_id BETWEEN a1.left_id AND a1.right_id';
			break;

			default:
				$condition = 'a2.left_id BETWEEN a1.left_id AND a1.right_id OR a1.left_id BETWEEN a2.left_id AND a2.right_id';
			break;
		}

		$rows = array();

		$sql = 'SELECT a2.*
			FROM ' . $this->table_albums . ' a1
			LEFT JOIN ' . $this->table_albums . ' a2 ON (' . $condition .') AND a2.album_user_id = ' . (int) $branch_user_id .'
			WHERE a1.album_id = ' . (int) $album_id . '
				AND a1.album_user_id = ' . (int) $branch_user_id . '
			ORDER BY a2.left_id ' . (($order == 'descending') ? 'ASC' : 'DESC');
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			if (!$include_album && $row['album_id'] == $album_id)
			{
				continue;
			}

			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	 * Create album navigation links for given album, create parent
	 * list if currently null, assign basic album info to template
	 *
	 * borrowed from phpBB3
	 * @author: phpBB Group
	 * @function: generate_forum_nav
	 * @param $album_data
	 */
	public function generate_navigation($album_data)
	{
		// Add gallery menu entry
		// TO DO !!! THIS SHOULD BE MOVED TO MENU CREATOR!!
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'   => $this->user->lang('GALLERY'),
			'U_VIEW_FORUM'   => $this->helper->route('phpbbgallery_core_index'),
		));
		// Get album parents
		$album_parents = $this->get_parents($album_data);

		// Display username for personal albums
		if ($album_data['album_user_id'] > \phpbbgallery\core\block::PUBLIC_ALBUM)
		{
			$sql = 'SELECT user_id, username, user_colour
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $album_data['album_user_id'];
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $this->user->lang('PERSONAL_ALBUMS'),
					'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_personal'),
				));
			}
			$this->db->sql_freeresult($result);
		}

		// Build navigation links
		if (!empty($album_parents))
		{
			foreach ($album_parents as $parent_album_id => $parent_data)
			{
				list($parent_name, $parent_type) = array_values($parent_data);

				$this->template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $parent_name,
					'FORUM_ID'		=> $parent_album_id,
					'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $parent_album_id)),
				));
			}
		}

		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $album_data['album_name'],
			'FORUM_ID'		=> $album_data['album_id'],
			'U_VIEW_FORUM'	=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_data['album_id'])),
		));

		$this->template->assign_vars(array(
			'ALBUM_ID' 		=> $album_data['album_id'],
			'ALBUM_NAME'	=> $album_data['album_name'],
			'ALBUM_DESC'	=> generate_text_for_display($album_data['album_desc'], $album_data['album_desc_uid'], $album_data['album_desc_bitfield'], $album_data['album_desc_options']),
			//'ALBUM_CONTEST_START'	=> ($album_data['contest_id']) ? $this->user->lang('CONTEST_START' . ((($album_data['contest_start']) < time())? 'ED' : 'S'), $this->user->format_date(($album_data['contest_start']), false, true)) : '',
			//'ALBUM_CONTEST_RATING'	=> ($album_data['contest_id']) ? $this->user->lang('CONTEST_RATING_START' . ((($album_data['contest_start'] + $album_data['contest_rating']) < time())? 'ED' : 'S'), $this->user->format_date(($album_data['contest_start'] + $album_data['contest_rating']), false, true)) : '',
			//'ALBUM_CONTEST_END'		=> ($album_data['contest_id']) ? $this->user->lang('CONTEST_END' . ((($album_data['contest_start'] + $album_data['contest_end']) < time())? 'ED' : 'S'), $this->user->format_date(($album_data['contest_start'] + $album_data['contest_end']), false, true)) : '',
			'U_VIEW_ALBUM'	=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $album_data['album_id'])),
		));

		return;
	}

	/**
	 * Returns album parents as an array. Get them from album_data if available, or update the database otherwise
	 *
	 * borrowed from phpBB3
	 * @author: phpBB Group
	 * @function: get_forum_parents
	 * @param $album_data
	 * @return array|mixed
	 */
	public function get_parents($album_data)
	{
		$album_parents = array();
		if ($album_data['parent_id'] > 0)
		{
			if ($album_data['album_parents'] == '')
			{
				$sql = 'SELECT album_id, album_name, album_type
					FROM ' . $this->table_albums . '
					WHERE left_id < ' . (int) $album_data['left_id'] . '
						AND right_id > ' . (int) $album_data['right_id'] . '
						AND album_user_id = ' . (int) $album_data['album_user_id'] . '
					ORDER BY left_id ASC';
				$result = $this->db->sql_query($sql);

				while ($row = $this->db->sql_fetchrow($result))
				{
					$album_parents[$row['album_id']] = array($row['album_name'], (int) $row['album_type']);
				}
				$this->db->sql_freeresult($result);

				$album_data['album_parents'] = serialize($album_parents);

				$sql = 'UPDATE ' . $this->table_albums . "
					SET album_parents = '" . $this->db->sql_escape($album_data['album_parents']) . "'
					WHERE parent_id = " . (int) $album_data['parent_id'];
				$this->db->sql_query($sql);
			}
			else
			{
				$album_parents = unserialize($album_data['album_parents']);
			}
		}

		return $album_parents;
	}

	/**
	 * Obtain list of moderators of each album
	 *
	 * borrowed from phpBB3
	 * @author: phpBB Group
	 * @function: get_forum_moderators
	 * @param bool $album_id
	 * @return array
	 */
	public function get_moderators($album_id = false)
	{
		$album_id_ary = $album_moderators = array();

		if ($album_id !== false)
		{
			if (!is_array($album_id))
			{
				$album_id = array($album_id);
			}

			// Exchange key/value pair to be able to faster check for the album id existence
			$album_id_ary = array_flip($album_id);
		}

		$sql_array = array(
			'SELECT'	=> 'm.*, u.user_colour, g.group_colour, g.group_type',
			'FROM'		=> array($this->table_moderators => 'm'),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'm.user_id = u.user_id',
				),
				array(
					'FROM'	=> array(GROUPS_TABLE => 'g'),
					'ON'	=> 'm.group_id = g.group_id',
				),
			),

			'WHERE'		=> 'm.display_on_index = 1',
			'ORDER_BY'	=> 'm.group_id ASC, m.user_id ASC',
		);

		// We query every album here because for caching we should not have any parameter.
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$a_id = (int) $row['album_id'];

			if (!isset($album_id_ary[$a_id]))
			{
				continue;
			}

			if (!empty($row['user_id']))
			{
				$album_moderators[$a_id][] = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
			}
			else
			{
				$group_name = (($row['group_type'] == GROUP_SPECIAL) ? $this->user->lang('G_' . $row['group_name']) : $row['group_name']);

				if ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'))
				{
					$album_moderators[$a_id][] = '<span' . (($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . ';"' : '') . '>' . $group_name . '</span>';
				}
				else
				{
					$album_moderators[$a_id][] = '<a' . (($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . ';"' : '') . ' href="' . append_sid($this->root_path . 'memberlist.' . $this->php_ext, 'mode=group&amp;g=' . $row['group_id']) . '">' . $group_name . '</a>';
				}
			}
		}
		$this->db->sql_freeresult($result);

		return $album_moderators;
	}

	/**
	 * Display albums
	 *
	 * borrowed from phpBB3
	 * @author: phpBB Group
	 * @function: display_forums
	 * @param string $root_data
	 * @param bool $display_moderators
	 * @param bool $return_moderators
	 * @return array
	 */
	public function display_albums($root_data = '', $display_moderators = true, $return_moderators = false)
	{
		$album_rows = $subalbums = $album_ids = $album_ids_moderator = $album_moderators = $active_album_ary = array();
		$parent_id = $visible_albums = 0;
		//$mode = $this->request->variable('mode', '');
		$mode = $this->album_mode;
		// Mark albums read?
		$mark_read = $this->request->variable('mark', '');

		if ($mark_read == 'all')
		{
			$mark_read = '';
		}

		if (!$root_data)
		{
			if ($mark_read == 'albums')
			{
				$mark_read = 'all';
			}
			$root_data = array('album_id' => \phpbbgallery\core\block::PUBLIC_ALBUM);
			$sql_where = 'a.album_user_id = ' . \phpbbgallery\core\block::PUBLIC_ALBUM;
		}
		else if ($root_data == 'personal')
		{
			if ($mark_read == 'albums')
			{
				$mark_read = 'all';
			}
			$root_data = array('album_id' => 0);
			$sql_where = 'a.album_user_id > ' . \phpbbgallery\core\block::PUBLIC_ALBUM;
			$num_pegas = $this->config['phpbb_gallery_num_pegas'];
			$first_char = $this->request->variable('first_char', '');
			if ($first_char == 'other')
			{
				// Loop the ASCII: a-z
				for ($i = 97; $i < 123; $i++)
				{
					$sql_where .= ' AND u.username_clean NOT ' . $this->db->sql_like_expression(chr($i) . $this->db->any_char);
				}
			}
			else if ($first_char)
			{
				$sql_where .= ' AND u.username_clean ' . $this->db->sql_like_expression(substr($first_char, 0, 1) . $this->db->any_char);
			}

			if ($first_char)
			{
				// We do not view all personal albums, so we need to recount, for the pagination.
				$sql_array = array(
					'SELECT'		=> 'count(a.album_id) as pgalleries',
					'FROM'			=> array($this->table_albums => 'a'),

					'LEFT_JOIN'		=> array(
						array(
							'FROM'		=> array(USERS_TABLE => 'u'),
							'ON'		=> 'u.user_id = a.album_user_id',
						),
					),

					'WHERE'			=> 'a.parent_id = 0 AND ' . $sql_where,
				);
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$num_pegas = $this->db->sql_fetchfield('pgalleries');
				$this->db->sql_freeresult($result);
			}

			$mode_personal = true;
			$start = $this->album_start;
			//$limit = $this->config['phpbb_gallery_pegas_per_page'];
			$limit = $this->album_limit;
			/*$this->template->assign_vars(array(
				'PAGINATION'				=> $this->pagination->generate_template_pagination(array(
						//todo 'mode=' . $mode . (($first_char) ? '&amp;first_char=' . $first_char : '')
					), 'pagination', 'page', $num_pegas, $limit, $start),
				'TOTAL_PGALLERIES_SHORT'	=> $this->user->lang('TOTAL_PEGAS_SHORT_SPRINTF', $num_pegas),
				'PAGE_NUMBER'				=> $this->pagination->on_page($num_pegas, $limit, $start),
			));
			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_core_search_recent',
					'phpbbgallery_core_search_recent_page',),
					'params' => array()), 'pagination', 'page', $num_pegas, $limit, $start
			);*/
		}
		else
		{
			$sql_where = 'a.left_id > ' . $root_data['left_id'] . ' AND a.left_id < ' . $root_data['right_id'] . ' AND a.album_user_id = ' . $root_data['album_user_id'];
		}

		$sql_array = array(
			'SELECT'	=> 'a.*, at.mark_time',
			'FROM'		=> array($this->table_albums => 'a'),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->table_tracking => 'at'),
					'ON'	=> 'at.user_id = ' . $this->user->data['user_id'] . ' AND a.album_id = at.album_id'
				)
			),

			'ORDER_BY'	=> 'a.album_user_id, a.left_id',
		);

		if (isset($mode_personal))
		{
			$sql_array['LEFT_JOIN'][] = array(
				'FROM'	=> array(USERS_TABLE => 'u'),
				'ON'	=> 'u.user_id = a.album_user_id',
			);
			$sql_array['ORDER_BY'] = 'u.username_clean, a.left_id';
		}

		$sql_array['LEFT_JOIN'][] = array(
			'FROM'	=> array($this->table_contests => 'c'),
			'ON'	=> 'c.contest_album_id = a.album_id',
		);
		$sql_array['SELECT'] = $sql_array['SELECT'] . ', c.contest_marked';

		$sql = $this->db->sql_build_query('SELECT', array(
			'SELECT'	=> $sql_array['SELECT'],
			'FROM'		=> $sql_array['FROM'],
			'LEFT_JOIN'	=> $sql_array['LEFT_JOIN'],
			'WHERE'		=> $sql_where,
			'ORDER_BY'	=> $sql_array['ORDER_BY'],
		));

		$result = $this->db->sql_query($sql);

		$album_tracking_info = array();
		$branch_root_id = $root_data['album_id'];
		$zebra_array = $this->gallery_auth->get_user_zebra($this->user->data['user_id']);
		$listable = $this->gallery_auth->acl_album_ids('a_list');
		while ($row = $this->db->sql_fetchrow($result))
		{
			$album_id = $row['album_id'];
			//if user has no right to see the album - scip it here!
			if (!in_array($album_id, $listable))
			{
				continue;
			}
			// Mark albums read?
			if ($mark_read == 'albums' || $mark_read == 'all')
			{
				if ($this->gallery_auth->acl_check('a_list', $album_id, $row['album_user_id']))
				{
					$album_ids[] = $album_id;
					continue;
				}
			}

			// Category with no members
			if (!$row['album_type'] && ($row['left_id'] + 1 == $row['right_id']))
			{
				continue;
			}

			// Skip branch
			if (isset($right_id))
			{
				if ($row['left_id'] < $right_id)
				{
					continue;
				}
				unset($right_id);
			}

			// if this is invizible due zebra ... make it go away
			if ($this->gallery_auth->get_zebra_state($zebra_array, (int) $row['album_user_id'], (int) $row['album_id']) < (int) $row['album_auth_access'])
			{
				continue;
			}

			if (false)//@todo !$this->gallery_auth->acl_check('a_list', $album_id, $row['album_user_id']))
			{
				// if the user does not have permissions to list this album, skip everything until next branch
				$right_id = $row['right_id'];
				continue;
			}

			$album_tracking_info[$album_id] = (!empty($row['mark_time'])) ? $row['mark_time'] : $this->gallery_user->get_data('user_lastmark');

			if ($row['parent_id'] == $root_data['album_id'] || $row['parent_id'] == $branch_root_id)
			{
				if ($row['album_type'])
				{
					$album_ids_moderator[] = (int) $album_id;
				}

				// Direct child of current branch
				$parent_id = $album_id;
				$album_rows[$album_id] = $row;

				if (!$row['album_type'] && $row['parent_id'] == $root_data['album_id'])
				{
					$branch_root_id = $album_id;
				}
				$album_rows[$parent_id]['album_id_last_image'] = $row['album_id'];
				$album_rows[$parent_id]['album_type_last_image'] = $row['album_type'];
				$album_rows[$parent_id]['album_contest_marked'] = $row['contest_marked'];
				$album_rows[$parent_id]['orig_album_last_image_time'] = $row['album_last_image_time'];
			}
			else if ($row['album_type'])
			{
				$subalbums[$parent_id][$album_id]['display'] = ($row['display_on_index']) ? true : false;
				$subalbums[$parent_id][$album_id]['name'] = $row['album_name'];
				$subalbums[$parent_id][$album_id]['orig_album_last_image_time'] = $row['album_last_image_time'];
				$subalbums[$parent_id][$album_id]['children'] = array();

				if (isset($subalbums[$parent_id][$row['parent_id']]) && !$row['display_on_index'])
				{
					$subalbums[$parent_id][$row['parent_id']]['children'][] = $album_id;
				}

				$album_rows[$parent_id]['album_images'] += $row['album_images'];
				$album_rows[$parent_id]['album_images_real'] += $row['album_images_real'];

				if ($row['album_last_image_time'] > $album_rows[$parent_id]['album_last_image_time'])
				{
					$album_rows[$parent_id]['album_last_image_id'] = $row['album_last_image_id'];
					$album_rows[$parent_id]['album_last_image_name'] = $row['album_last_image_name'];
					$album_rows[$parent_id]['album_last_image_time'] = $row['album_last_image_time'];
					$album_rows[$parent_id]['album_last_user_id'] = $row['album_last_user_id'];
					$album_rows[$parent_id]['album_last_username'] = $row['album_last_username'];
					$album_rows[$parent_id]['album_last_user_colour'] = $row['album_last_user_colour'];
					$album_rows[$parent_id]['album_type_last_image'] = $row['album_type'];
					$album_rows[$parent_id]['album_contest_marked'] = $row['contest_marked'];
					$album_rows[$parent_id]['album_id_last_image'] = $album_id;
				}
			}
		}
		$this->db->sql_freeresult($result);

		// Handle marking albums
		if ($mark_read == 'albums' || $mark_read == 'all')
		{
			$redirect = build_url('mark');
			$token = $this->request->variable('hash', '');
			if (check_link_hash($token, 'global'))
			{
				if ($mark_read == 'all')
				{
					$this->misc->markread('all');
					$message = $this->user->lang('RETURN_INDEX', '<a href="' . $redirect . '">', '</a>');
				}
				else
				{
					$this->misc->markread('albums', $album_ids);
					$message = $this->user->lang('RETURN_ALBUM', '<a href="' . $redirect . '">', '</a>');
				}
				meta_refresh(3, $redirect);
				trigger_error($this->user->lang('ALBUMS_MARKED') . '<br /><br />' . $message);
			}
			else
			{
				$message = $this->user->lang('RETURN_PAGE', '<a href="' . $redirect . '">', '</a>');
				meta_refresh(3, $redirect);
				trigger_error($message);
			}
		}

		// Grab moderators ... if necessary
		if ($display_moderators)
		{
			if ($return_moderators)
			{
				$album_ids_moderator[] = $root_data['album_id'];
			}
			$this->get_moderators($album_moderators);
		}

		// Used to tell whatever we have to create a dummy category or not.
		$last_catless = true;
		foreach ($album_rows as $row)
		{
			// Empty category
			if (($row['parent_id'] == $root_data['album_id']) && ($row['album_type'] == \phpbbgallery\core\block::TYPE_CAT))
			{
				$this->template->assign_block_vars('albumrow', array(
					'S_IS_CAT'				=> true,
					'ALBUM_ID'				=> $row['album_id'],
					'ALBUM_NAME'			=> $row['album_name'],
					'ALBUM_DESC'			=> generate_text_for_display($row['album_desc'], $row['album_desc_uid'], $row['album_desc_bitfield'], $row['album_desc_options']),
					'ALBUM_FOLDER_IMG'		=> '',
					'ALBUM_FOLDER_IMG_SRC'	=> '',
					// 'ALBUM_IMAGE'			=> ($row['album_image']) ? $row['album_image'] : '',
					'U_VIEWALBUM'			=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $row['album_id'])),
				));

				continue;
			}

			$visible_albums++;
			if (($mode == 'personal') && (($visible_albums <= $start) || ($visible_albums > ($start + $limit))))
			{
				continue;
			}

			$album_id = $row['album_id'];
			$album_unread = (isset($album_tracking_info[$album_id]) && ($row['orig_album_last_image_time'] > $album_tracking_info[$album_id]) && ($this->user->data['user_id'] != ANONYMOUS)) ? true : false;

			$folder_alt = $l_subalbums = '';
			$subalbums_list = array();

			// Generate list of subalbums if we need to
			if (isset($subalbums[$album_id]))
			{
				foreach ($subalbums[$album_id] as $subalbum_id => $subalbum_row)
				{
					$subalbum_unread = (isset($album_tracking_info[$subalbum_id]) && $subalbum_row['orig_album_last_image_time'] > $album_tracking_info[$subalbum_id] && ($this->user->data['user_id'] != ANONYMOUS)) ? true : false;

					if (!$subalbum_unread && !empty($subalbum_row['children']) && ($this->user->data['user_id'] != ANONYMOUS))
					{
						foreach ($subalbum_row['children'] as $child_id)
						{
							if (isset($album_tracking_info[$child_id]) && $subalbums[$album_id][$child_id]['orig_album_last_image_time'] > $album_tracking_info[$child_id])
							{
								// Once we found an unread child album, we can drop out of this loop
								$subalbum_unread = true;
								break;
							}
						}
					}

					if ($subalbum_row['display'] && $subalbum_row['name'])
					{
						$subalbums_list[] = array(
							'link'		=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $subalbum_id)),
							'name'		=> $subalbum_row['name'],
							'unread'	=> $subalbum_unread,
						);
					}
					else
					{
						unset($subalbums[$album_id][$subalbum_id]);
					}

					if ($subalbum_unread)
					{
						$album_unread = true;
					}
				}

				$l_subalbums = (sizeof($subalbums[$album_id]) == 1) ? $this->user->lang('SUBALBUM') : $this->user->lang('SUBALBUMS');
				$folder_image = ($album_unread) ? 'forum_unread_subforum' : 'forum_read_subforum';
			}
			else
			{
				$folder_alt = ($album_unread) ? 'NEW_IMAGES' : 'NO_NEW_IMAGES';
				$folder_image = ($album_unread) ? 'forum_unread' : 'forum_read';
			}
			if ($row['album_status'] == \phpbbgallery\core\block::ALBUM_LOCKED)
			{
				$folder_image = ($album_unread) ? 'forum_unread_locked' : 'forum_read_locked';
				$folder_alt = 'ALBUM_LOCKED';
			}

			// Create last post link information, if appropriate
			if ($row['album_last_image_id'])
			{
				$lastimage_time = $this->user->format_date($row['album_last_image_time']);
				$lastimage_album_type = $row['album_type_last_image'];
				$lastimage_contest_marked = $row['album_contest_marked'];
				// phpbb_ext_gallery_core_image::generate_link('fake_thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $lastimage_image_id, $lastimage_name, $lastimage_album_id);
				$lastimage_uc_fake_thumbnail = $row['album_image'] ? generate_board_url() . '/' . $row['album_image'] : $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $row['album_last_image_id']));
				// phpbb_ext_gallery_core_image::generate_link('thumbnail', $phpbb_ext_gallery->config->get('link_thumbnail'), $lastimage_image_id, $lastimage_name, $lastimage_album_id);
				$lastimage_uc_thumbnail = $row['album_image'] ? generate_board_url() . '/' . $row['album_image'] : $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => $row['album_last_image_id']));
				// phpbb_ext_gallery_core_image::generate_link('image_name', $phpbb_ext_gallery->config->get('link_image_name'), $lastimage_image_id, $lastimage_name, $lastimage_album_id);
				$lastimage_uc_name = '';//@todo phpbb_ext_gallery_core_image::generate_link('image_name', $phpbb_ext_gallery->config->get('link_image_name'), $lastimage_image_id, $lastimage_name, $lastimage_album_id);
				// phpbb_ext_gallery_core_image::generate_link('lastimage_icon', $phpbb_ext_gallery->config->get('link_image_icon'), $lastimage_image_id, $lastimage_name, $lastimage_album_id);
				$lastimage_uc_icon = '';//@todo phpbb_ext_gallery_core_image::generate_link('lastimage_icon', $phpbb_ext_gallery->config->get('link_image_icon'), $lastimage_image_id, $lastimage_name, $lastimage_album_id);
			}
			else
			{
				$lastimage_time = $lastimage_album_type = $lastimage_contest_marked = 0;
				$lastimage_uc_fake_thumbnail = $lastimage_uc_thumbnail = $lastimage_uc_name = $lastimage_uc_icon = '';
				$lastimage_uc_fake_thumbnail = $lastimage_uc_thumbnail = $this->helper->route('phpbbgallery_core_image_file_mini', array('image_id' => 0));
			}

			// Output moderator listing ... if applicable
			$l_moderator = $moderators_list = '';
			if ($display_moderators && !empty($album_moderators[$album_id]))
			{
				$l_moderator = (sizeof($album_moderators[$album_id]) == 1) ? $this->user->lang('MODERATOR') : $this->user->lang('MODERATORS');
				$moderators_list = implode(', ', $album_moderators[$album_id]);
			}

			$s_subalbums_list = array();
			foreach ($subalbums_list as $subalbum)
			{
				$s_subalbums_list[] = '<a href="' . $subalbum['link'] . '" class="subforum ' . (($subalbum['unread']) ? 'unread' : 'read') . '" title="' . (($subalbum['unread']) ? $this->user->lang('NEW_IMAGES') : $this->user->lang('NO_NEW_IMAGES')) . '">' . $subalbum['name'] . '</a>';
			}
			$s_subalbums_list = (string) implode(', ', $s_subalbums_list);
			$catless = ($row['parent_id'] == $root_data['album_id']) ? true : false;

			$s_username_hidden = ($lastimage_album_type == \phpbbgallery\core\block::TYPE_CONTEST) && $lastimage_contest_marked && !$this->gallery_auth->acl_check('m_status', $album_id, $row['album_user_id']) && ($this->user->data['user_id'] != $row['album_last_user_id'] || $row['album_last_user_id'] == ANONYMOUS);

			$this->template->assign_block_vars('albumrow', array(
				'S_IS_CAT'			=> false,
				'S_NO_CAT'			=> $catless && !$last_catless,
				'S_LOCKED_ALBUM'	=> ($row['album_status'] == \phpbbgallery\core\block::ALBUM_LOCKED) ? true : false,
				'S_UNREAD_ALBUM'	=> ($album_unread) ? true : false,
				'S_LIST_SUBALBUMS'	=> ($row['display_subalbum_list']) ? true : false,
				'S_SUBALBUMS'		=> (sizeof($subalbums_list)) ? true : false,

				'ALBUM_ID'				=> $row['album_id'],
				'ALBUM_NAME'			=> $row['album_name'],
				'ALBUM_DESC'			=> generate_text_for_display($row['album_desc'], $row['album_desc_uid'], $row['album_desc_bitfield'], $row['album_desc_options']),
				'IMAGES'				=> $row['album_images'],
				'UNAPPROVED_IMAGES'		=> ($this->gallery_auth->acl_check('m_status', $album_id, $row['album_user_id'])) ? ($row['album_images_real'] - $row['album_images']) : 0,
				'ALBUM_IMG_STYLE'		=> $folder_image,
				'ALBUM_FOLDER_IMG'		=> $this->user->img($folder_image, $folder_alt),
				'ALBUM_FOLDER_IMG_ALT'	=> isset($this->user->lang[$folder_alt]) ? $this->user->lang($folder_alt) : '',
				//'ALBUM_IMAGE'			=> ($row['album_image']) ? $row['album_image'] : '',
				'LAST_IMAGE_TIME'		=> $lastimage_time,
				'LAST_USER_FULL'		=> ($s_username_hidden) ? $this->user->lang('CONTEST_USERNAME') : get_username_string('full', $row['album_last_user_id'], $row['album_last_username'], $row['album_last_user_colour']),
				'UC_THUMBNAIL'			=> $this->config['phpbb_gallery_mini_thumbnail_disp'] ? $lastimage_uc_thumbnail : '',
				'UC_FAKE_THUMBNAIL'		=> $this->config['phpbb_gallery_mini_thumbnail_disp'] ? $lastimage_uc_fake_thumbnail : '',
				'UC_IMAGE_NAME'			=> $lastimage_uc_name,
				'UC_LASTIMAGE_ICON'		=> $lastimage_uc_icon,
				'ALBUM_COLOUR'			=> get_username_string('colour', $row['album_last_user_id'], $row['album_last_username'], $row['album_last_user_colour']),
				'MODERATORS'			=> $moderators_list,
				'SUBALBUMS'				=> $s_subalbums_list,

				'L_SUBALBUM_STR'		=> $l_subalbums,
				'L_ALBUM_FOLDER_ALT'	=> $folder_alt,
				'L_MODERATOR_STR'		=> $l_moderator,

				'U_VIEWALBUM'			=> $this->helper->route('phpbbgallery_core_album', array('album_id' => $row['album_id'])),
			));

			// Assign subforums loop for style authors
			foreach ($subalbums_list as $subalbum)
			{
				$this->template->assign_block_vars('albumrow.subalbum', array(
					'U_SUBALBUM'	=> $subalbum['link'],
					'SUBALBUM_NAME'	=> $subalbum['name'],
					'S_UNREAD'		=> $subalbum['unread'],
				));
			}

			$last_catless = $catless;
		}

		$this->template->assign_vars(array(
			'U_MARK_ALBUMS'		=> ($this->user->data['is_registered']) ? $this->helper->route('phpbbgallery_core_album', array('album_id' => $root_data['album_id'], 'hash' => generate_link_hash('global'), 'mark' => 'albums')) : '',
			'S_HAS_SUBALBUM'	=> ($visible_albums) ? true : false,
			'L_SUBFORUM'		=> ($visible_albums == 1) ? $this->user->lang('SUBALBUM') : $this->user->lang('SUBALBUMS'),
			'LAST_POST_IMG'		=> $this->user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
			'FAKE_THUMB_SIZE'	=> $this->config['phpbb_gallery_mini_thumbnail_size'],
		));

		if ($return_moderators)
		{
			return array($active_album_ary, $album_moderators);
		}

		$this->albums_total = $visible_albums;

		return array($active_album_ary, array());
	}
}
