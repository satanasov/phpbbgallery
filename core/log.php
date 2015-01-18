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
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\user_loader $user_loader, \phpbb\template\template $template,
	\phpbb\controller\helper $helper, \phpbb\pagination $pagination,
	$log_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->user_loader = $user_loader;
		$this->template = $template;
		$this->helper = $helper;
		$this->pagination = $pagination;
		$this->log_table = $log_table;
	}

	/**
	* Add item to log
	*
	* @param	(string)	$log_type		type of action (user/mod/admin/system) max 16 chars
	* @param	(string)	$log_action		action we are loging (add/remove/approve/unaprove/delete) max 32 chars
	* @param	(int)		$album			Album we are loging for (can be 0)
	* @param	(int)		$image			Image we are loging for (can be 0)
	* @param	(string)	$description	Description sting
	*/

	public function add_log($log_type, $log_action, $album = 0, $image = 0, $description = array())
	{
		$user = (int) $this->user->data['user_id'];
		$time = (int) time();

		$sql = 'INSERT INTO ' . $this->log_table . ' (log_time, log_type, log_action, log_user, log_ip, album, image, description)
		VALUES (
			' . (int) $time . ',
			\'' . $this->db->sql_escape($log_type) . '\',
			\'' . $this->db->sql_escape($log_action) . '\',
			' . (int) $user . ',
			\'' . $this->db->sql_escape($this->user->ip) . '\',
			' . (int) $album . ',
			' . (int) $image . ',
			\'' . $this->db->sql_escape(json_encode($description)) . '\'
		)';
		$this->db->sql_query($sql);
	}

	/**
	* Build log list
	*
	* @param	(string)	$type	Type of queue to build user/mod/admin/system
	* @param	(int)		$limit	How many items to show
	* @param	(int)		$start	start count used to build paging
	*/
	public function build_list($type, $limit = 25, $page = 1)
	{
		$this->user->add_lang_ext('phpbbgallery/core', array('info_acp_gallery_logs'));

		$sql = 'SELECT count(log_id) as count FROM ' . $this->log_table . ' WHERE log_type = \'' . $this->db->sql_escape($type) . '\' ORDER BY log_id DESC';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$count = $row['count'];

		$sql = 'SELECT * FROM ' . $this->log_table . ' WHERE log_type = \'' . $this->db->sql_escape($type) . '\' ORDER BY log_id DESC';
		$result = $this->db->sql_query_limit($sql, $limit, ($page - 1) * $limit);

		$logouput = $users_array = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$logoutput[] = array(
				'id'	=> $row['log_id'],
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
		foreach($logoutput as $var)
		{
			$description = '';
			switch ($type)
			{
				case 'moderator':
					switch ($var['action'])
					{
						case 'move':
							$description = $this->user->lang($var['description'][0], $var['description'][1], $var['description'][2]);
						break;
						default:
							$description = $this->user->lang($var['description'][0], $var['description'][1]);
						break;
					}
				break;
			}
			$this->template->assign_block_vars('log', array(
				'U_LOG_ID'		=> $var['id'],
				'U_LOG_USER'	=> $this->user_loader->get_username($var['user'], 'full'),
				'U_LOG_IP'		=> $var['ip'],
				'U_ALBUM_LINK'	=> $var['album'] != 0 ? $this->helper->route('phpbbgallery_album', array('album_id'	=> $var['album'])) : false,
				'U_IMAGE_LINK'	=> $var['image'] != 0 ? $this->helper->route('phpbbgallery_image', array('image_id'	=> $var['image'])) : false,
				'U_LOG_ACTION'	=> $description,
				'U_TIME'		=> $this->user->format_date($var['time']),
			));
		}
		$this->template->assign_vars(array(
			'S_HAS_LOGS' => $count > 0 ? true : false,
			'TOTAL_PAGES'	=> $this->user->lang('PAGE_TITLE_NUMBER', $page),
		));
		$this->pagination->generate_template_pagination(array(
			'routes' => array(
				'phpbbgallery_moderate_action_log',
				'phpbbgallery_moderate_action_log_page',
			),
			'params' => array(
			),
		), 'pagination', 'page', $count, $limit, ($page-1) * $limit);
	}
}
