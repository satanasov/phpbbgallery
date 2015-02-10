<?php
/**
*
* @package phpBB Gallery Extension
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace phpbbgallery\core\notification;

use Symfony\Component\DependencyInjection\Container;

class helper
{
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user,
	\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\album\loader $album_load, \phpbb\controller\helper $helper, \phpbbgallery\core\url $url,
	Container $phpbb_container, $root_path, $php_ext, $watch_table)
	{
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->gallery_auth = $gallery_auth;
		$this->album_load = $album_load;
		$this->helper = $helper;
		$this->url = $url;
		$this->phpbb_container = $phpbb_container;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->watch_table = $watch_table;
	}

	/**
	* Main notification function
	* @param type			Type of notification (add/confirm)
	* @param notify_user	User to notify
	* @param action_user	User that trigered the action
	*/
	public function notify($type, $target)
	{
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		switch ($type)
		{
			case 'approval':
				$targets = $this->gallery_auth->acl_users_ids('i_approve', $target['album_id']);
				var_dump($targets);
				$album_data = $this->album_load->get($target['album_id']);
				$notification_data = array(
					'user_ids' => $targets,
					'album_id' => $target['album_id'],
					'album_name' => $album_data['album_name'],
					'last_image_id'	=> $target['last_image'],
					'uploader'	=> $target['uploader'],
					'album_url'	=> $this->url->get_uri($this->helper->route('phpbbgallery_album', array('album_id' => $target['album_id']))),
				);
				$phpbb_notifications->add_notifications('notification.type.phpbbgallery_image_for_approval', $notification_data);
			break;
			///case 'add':
			//	$phpbb_notifications->add_notifications('notification.type.zebraadd', $notification_data);
			//break;
			//case 'confirm':
			//	$phpbb_notifications->add_notifications('notification.type.zebraconfirm', $notification_data);
			//break;
		}
	}
	public function read($type, $target)
	{
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		switch ($type)
		{
			case 'approval':
				$phpbb_notifications->mark_notifications_read_by_parent('notification.type.phpbbgallery_image_for_approval', $target, false);
			break;
		}
	}
	public function clean($user1, $user2)
	{
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		$phpbb_notifications->delete_notifications('notification.type.zebraadd', $user1, $user2);
		$phpbb_notifications->delete_notifications('notification.type.zebraadd', $user2, $user1);
		$phpbb_notifications->delete_notifications('notification.type.zebraconfirm', $user2, $user1);
		$phpbb_notifications->delete_notifications('notification.type.zebraconfirm', $user1, $user2);
	}

	/**
	* Get watched for album
	*
	* @param (int) $album_id	Album we check
	* @param (int) $user_id = false	User we check for
	*/
	public function get_watched_album($album_id, $user_id = false)
	{
		if (!$user_id)
		{
			$user_id = $this->user->data['user_id'];
		}
		$sql = 'SELECT COUNT(watch_id) as count FROM ' . $this->watch_table . ' WHERE album_id = ' . (int) $album_id . ' and user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		return $row['count'];
	}

	/**
	* Add albums to watch-list
	*
	* @param	mixed	$album_ids		Array or integer with album_id where we delete from the watch-list.
	* @param	int		$user_id		If not set, it uses the currents user_id
	*/
	public function add_albums($album_ids, $user_id = false)
	{
		$album_ids = $this->cast_mixed_int2array($album_ids);
		$user_id = (int) (($user_id) ? $user_id : $this->user->data['user_id']);

		// First check if we are not subscribed alredy for some
		$sql = 'SELECT * FROM ' . $this->watch_table . ' WHERE user_id = ' . $user_id . ' and ' . $this->db->sql_in_set('album_id', $album_ids);
		$result = $this->db->sql_query($sql);
		$exclude = array();
		while ($row = $this->db->sql_fetchrow($sql))
		{
			$exclude[] = (int) $row['album_id'];
		}
		$album_ids = array_diff($album_ids, $exclude);
		foreach ($album_ids as $album_id)
		{
			$sql_ary = array(
				'album_id'		=> $album_id,
				'user_id'		=> $user_id,
			);
			$sql = 'INSERT INTO ' . $this->watch_table . $this->db->sql_build_array('INSERT', $sql_ary);
			$this->db->sql_query($sql);
		}
	}

	/**
	* Remove albums from watch-list
	*
	* @param	mixed	$album_ids		Array or integer with album_id where we delete from the watch-list.
	* @param	mixed	$user_ids		If not set, it uses the currents user_id
	*/
	public function remove_albums($album_ids, $user_ids = false)
	{
		$album_ids = $this->cast_mixed_int2array($album_ids);
		$user_ids = $this->cast_mixed_int2array((($user_ids) ? $user_ids : $this->user->data['user_id']));

		$sql = 'DELETE FROM ' . $this->watch_table . '
			WHERE ' . $this->db->sql_in_set('user_id', $user_ids) . '
				AND ' . $this->db->sql_in_set('album_id', $album_ids);
		$this->db->sql_query($sql);
	}

	/**
	*
	* Cast int or array to array
	* @param (mixed) $ids
	*/
	static public function cast_mixed_int2array($ids)
	{
		if (is_array($ids))
		{
			return array_map('intval', $ids);
		}
		else
		{
			return array((int) $ids);
		}
	}
}
