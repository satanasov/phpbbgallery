<?php

/**
*
* @package phpBB Gallery
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core;

class log
{
	/**
	 * log constructor.
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\user $user
	 * @param \phpbb\user_loader $user_loader
	 * @param \phpbb\template\template $template
	 * @param \phpbb\controller\helper $helper
	 * @param \phpbb\pagination $pagination
	 * @param auth\auth $gallery_auth
	 * @param config $gallery_config
	 * @param $log_table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\user_loader $user_loader, \phpbb\template\template $template,
								\phpbb\controller\helper $helper, \phpbb\pagination $pagination, \phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\config $gallery_config,
								$log_table, $images_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->user_loader = $user_loader;
		$this->template = $template;
		$this->helper = $helper;
		$this->pagination = $pagination;
		$this->gallery_auth = $gallery_auth;
		$this->gallery_config = $gallery_config;
		$this->log_table = $log_table;
		$this->images_table = $images_table;
	}

	/**
	 * Add item to log
	 *
	 * @param   string			$log_type    type of action (user/mod/admin/system) max 16 chars
	 * @param   string			$log_action  action we are loging (add/remove/approve/unaprove/delete) max 32 chars
	 * @param 	int				$album
	 * @param   int				$image       Image we are loging for (can be 0)
	 * @param	array|string 	$description Description string
	 */
	public function add_log($log_type, $log_action, $album = 0, $image = 0, $description = array())
	{
		$user = (int) $this->user->data['user_id'];
		$time = (int) time();

		$sql_array = array(
			'log_time'		=> (int) $time,
			'log_type'		=> $this->db->sql_escape($log_type),
			'log_action'	=> $this->db->sql_escape($log_action),
			'log_user'		=> (int) $user,
			'log_ip'		=> $this->db->sql_escape($this->user->ip),
			'album'			=> (int) $album,
			'image'			=> (int) $image,
			'description'	=> $this->db->sql_escape(json_encode($description))
		);
		$sql = 'INSERT INTO ' . $this->log_table . ' ' . $this->db->sql_build_array('INSERT', $sql_array);
		$this->db->sql_query($sql);
	}

	/**
	* Delete logs
	* @param	array	$mark	Logs selected for deletion
	**/
	public function delete_logs($mark)
	{
		$sql = 'DELETE FROM ' . $this->log_table . ' WHERE ' . $this->db->sql_in_set('log_id', $mark);
		$this->db->sql_query($sql);
		$this->add_log('admin', 'log', 0, 0, array('LOG_CLEAR_GALLERY'));
	}

	/**
	 * Build log list
	 *
	 * @param   	string		$type  Type of queue to build user/mod/admin/system
	 * @param		int			$limit How many items to show
	 * @param		int			$page
	 * @param		int			$album
	 * @param		int			$image
	 * @param		array		$additional
	 * @internal	param int	$start start count used to build paging
	 */
	public function build_list($type, $limit = 0, $page = 1, $album = 0, $image = 0, $additional = array())
	{
		if ($limit == 0)
		{
			$limit = $this->gallery_config->get('items_per_page');
		}
		$this->user->add_lang_ext('phpbbgallery/core', array('info_acp_gallery_logs'));

		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$sql_array = array(
			'FROM'	=> array(
				$this->log_table	=> 'l',
				$this->images_table => 'i'
			),
		);
		$sql_where = array();
		if ($type != 'all')
		{
			$sql_where[] = 'l.log_type = \'' . $this->db->sql_escape($type) . '\'';
		}
		$mod_array = $this->gallery_auth->acl_album_ids('m_status');
		// Patch for missing album
		$mod_array[] = 0;
		// If album is -1 we are calling it from ACP so ... prority!
		// If album is 0 we are calling it from moderator log, so we need album we can access
		if ($album === 0)
		{
			// If no albums we can approve - quit building queue
			if (empty($mod_array))
			{
				return;
			}
			$sql_where[] = $this->db->sql_in_set('l.album', $mod_array);
			$sql_where[] = $this->db->sql_in_set('i.image_album_id', $mod_array);
		}
		if ($album > 0)
		{
			if (!$this->gallery_auth->acl_check('i_view', (int) $album))
			{
				return;
			}
			$sql_where[] = 'l.album = ' . (int) $album;
		}
		if ($image > 0)
		{
			$sql_where[] = 'l.image = ' . (int) $image;
			$sql_where[] = $this->db->sql_in_set('i.image_album_id', $mod_array);
		}
		if (isset($additional['sort_days']))
		{
			$sql_where[] = 'l.log_time > ' . (time() - ($additional['sort_days'] * 86400));
		}
		// And additional check for "active" logs (DB admin can review logs in DB)
		$sql_where[] = 'l.deleted = 0';
		$sql_array['WHERE'] = implode(' and ', $sql_where);
		if (isset($additional['sort_key']))
		{
			switch ($additional['sort_key'])
			{
				case 'u':
					$sql_array['ORDER_BY'] = 'l.log_user ' . (isset($additional['sort_dir']) ? 'ASC' : 'DESC');
					$sql_array['GROUP_BY'] = 'l.log_user, l.log_id';
				break;
				case 'i':
					$sql_array['ORDER_BY'] = 'l.log_ip ' . (isset($additional['sort_dir']) ? 'ASC' : 'DESC');
					$sql_array['GROUP_BY'] = 'l.log_ip, l.log_id';
				break;
				case 'o':
					$sql_array['ORDER_BY'] = 'l.description ' . (isset($additional['sort_dir']) ? 'ASC' : 'DESC');
					$sql_array['GROUP_BY'] = 'l.description, l.log_id';
				break;
			}
		}
		else
		{
			$sql_array['ORDER_BY'] = 'l.log_time ' . (isset($additional['sort_dir']) ? 'ASC' : 'DESC');
			$sql_array['GROUP_BY'] = 'l.log_time, l.log_id';
		}
		// So we need count - so define SELECT
		$sql_array['SELECT'] = 'count(l.log_id) as count';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];

		$sql_array['SELECT'] = '*';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $limit, ($page - 1) * $limit);

		$logoutput = $users_array = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$logoutput[] = array(
				'id'	=> $row['log_id'],
				'type'	=> $row['log_type'],
				'action'	=> $row['log_action'],
				'time'	=> $row['log_time'],
				'user'	=> $row['log_user'],
				'ip'	=> $row['log_ip'],
				'album'	=> $row['album'],
				'image'	=> $row['image'],
				'description'	=> json_decode($row['description'])
			);
			$users_array[$row['log_user']] = array('');
		}
		$this->db->sql_freeresult($result);

		$this->user_loader->load_users(array_keys($users_array));

		// Let's build template vars
		if (!empty($logoutput))
		{
			foreach ($logoutput as $var)
			{
				$this->template->assign_block_vars('log', array(
					'U_LOG_ID'		=> $var['id'],
					'U_LOG_USER'	=> $this->user_loader->get_username($var['user'], 'full'),
					'U_TYPE'		=> $var['type'],
					'U_LOG_IP'		=> $var['ip'],
					'U_ALBUM_LINK'	=> $var['album'] != 0 ? $this->helper->route('phpbbgallery_core_album', array('album_id'	=> $var['album'])) : false,
					'U_IMAGE_LINK'	=> $var['image'] != 0 ? $this->helper->route('phpbbgallery_core_image', array('image_id'	=> $var['image'])) : false,
					//'U_LOG_ACTION'	=> $description,
					'U_LOG_ACTION'	=> $description = $this->user->lang($var['description'][0], isset($var['description'][1]) ? $var['description'][1] : false, isset($var['description'][2]) ? $var['description'][2] : false, isset($var['description'][3]) ? $var['description'][3] : false),
					'U_TIME'		=> $this->user->format_date($var['time']),
				));
			}
		}
		$this->template->assign_vars(array(
			'S_HAS_LOGS' => $count > 0 ? true : false,
			'TOTAL_PAGES'	=> $this->user->lang('PAGE_TITLE_NUMBER', $page),
		));
		// Here we do some routes magic
		if ($album == 0)
		{
			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_core_moderate_action_log',
					'phpbbgallery_core_moderate_action_log_page',
				),
				'params' => array(
				),
			), 'pagination', 'page', $count, $limit, ($page-1) * $limit);
		}
		else if ($album == -1)
		{
			$url_array = array(
				'i' => '-phpbbgallery-core-acp-gallery_logs_module',
				'mode' => 'main',
			);
			if (isset($additional['sort_days']))
			{
				$url_array['st'] = $additional['sort_days'];
			}
			if (isset($additional['sort_key']))
			{
				$url_array['sk'] = $additional['sort_key'];
			}
			if (isset($additional['sort_dir']))
			{
				$url_array['sd'] = $additional['sort_dir'];
			}
			$url = http_build_query($url_array,'','&');

			$this->pagination->generate_template_pagination(append_sid('index.php?' . $url), 'pagination', 'page', $count, $limit, ($page-1) * $limit);
		}
		else
		{
			$this->pagination->generate_template_pagination(array(
				'routes' => array(
					'phpbbgallery_core_moderate_action_log_album',
					'phpbbgallery_core_moderate_action_log_album_page',
				),
				'params' => array(
					'album_id'	=> (int) $album,
				),
			), 'pagination', 'page', $count, $limit, ($page-1) * $limit);
		}
	}
}
