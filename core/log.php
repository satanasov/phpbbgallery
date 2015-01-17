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
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user,
	$log_table)
	{
		$this->db = $db;
		$this->user = $user;
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

		$sql = 'INSERT INTO ' . $this->log_table . ' (log_time, log_type, log_action, log_user, album, image, description)
		VALUES (
			' . (int) $time . ',
			\'' . $this->db->sql_escape($log_type) . '\',
			\'' . $this->db->sql_escape($log_action) . '\',
			' . (int) $user . ',
			' . (int) $album . ',
			' . (int) $image . ',
			\'' . $this->db->sql_escape(json_encode($description)) . '\'
		)';
		$this->db->sql_query($sql);
	}
}
