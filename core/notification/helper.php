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
				$targets = $this->gallery_auth->acl_users_ids('m_status', $target['album_id']);
				$album_data = $this->album_load->get($target['album_id']);
				$notification_data = array(
					'user_ids' => $targets,
					'album_id' => $target['album_id'],
					'album_name' => $album_data['album_name'],
					'last_image_id'	=> $target['last_image'],
					'uploader'	=> $target['uploader'],
					'album_url'	=> $this->url->get_uri($this->helper->route('phpbbgallery_core_album', array('album_id' => $target['album_id']))),
				);
				$phpbb_notifications->add_notifications('phpbbgallery.core.notification.image_for_approval', $notification_data);
			break;
			case 'approved':
				$targets = $target['targets'];
				$album_data = $this->album_load->get($target['album_id']);
				$notification_data = array(
					'user_ids' => $targets,
					'album_id' => $target['album_id'],
					'album_name' => $album_data['album_name'],
					'last_image_id'	=> $target['last_image'],
					'album_url'	=> $this->url->get_uri($this->helper->route('phpbbgallery_core_album', array('album_id' => $target['album_id']))),
				);
				$phpbb_notifications->add_notifications('phpbbgallery.core.notification.image_approved', $notification_data);
			break;
			case 'new_image':
				$targets = $target['targets'];
				$album_data = $this->album_load->get($target['album_id']);
				$notification_data = array(
					'user_ids' => $targets,
					'album_id' => $target['album_id'],
					'album_name' => $album_data['album_name'],
					'last_image_id'	=> $target['last_image'],
					'album_url'	=> $this->url->get_uri($this->helper->route('phpbbgallery_core_album', array('album_id' => $target['album_id']))),
				);
				$phpbb_notifications->add_notifications('phpbbgallery.core.notification.new_image', $notification_data);
			break;
			case 'new_comment':
				$notification_data = array(
					'user_ids'	=> array_diff($this->get_image_watchers($target['image_id']), array($target['poster_id'])),
					'image_id'	=> $target['image_id'],
					'comment_id'	=> $target['comment_id'],
					'poster'	=> $target['poster_id'],
					'url'		=> $this->url->get_uri($this->helper->route('phpbbgallery_core_image', array('image_id' => $target['image_id']))),
				);
				$phpbb_notifications->add_notifications('phpbbgallery.core.notification.new_comment', $notification_data);
			break;
			case 'new_report':
				if ($target['reported_album_id'] == 0)
				{
					$image_data = $this->image->get_image_data($target['reported_image_id']);
					$target['reported_album_id'] = $image_data['image_album_id'];
				}
				$notification_data = array(
					'user_ids'	=> array_diff($this->gallery_auth->acl_users_ids('m_report', $target['reported_album_id']), array($target['reporter_id'])),
					'item_id'	=> $target['report_id'],
					'reporter'	=> $target['reporter_id'],
					'url'		=> $this->url->get_uri($this->helper->route('phpbbgallery_core_moderate_image', array('image_id' => $target['reported_image_id']))),
				);
				$phpbb_notifications->add_notifications('phpbbgallery.core.notification.new_report', $notification_data);
			break;
			///case 'add':
			//	$phpbb_notifications->add_notifications('notification.type.zebraadd', $notification_data);
			//break;
			//case 'confirm':
			//	$phpbb_notifications->add_notifications('notification.type.zebraconfirm', $notification_data);
			//break;
		}
	}
	public function delete_notifications($type, $target)
	{
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		switch ($type)
		{
			case 'report':
				$phpbb_notifications->delete_notifications('phpbbgallery.core.notification.new_report', $target);
			break;
		}
	}

	// Read notification (in some cases it is needed)
	public function read($type, $target)
	{
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		switch ($type)
		{
			case 'approval':
				$phpbb_notifications->mark_notifications_read_by_parent('phpbbgallery.core.notification.image_for_approval', $target, false);
			break;
		}
	}

	/**
	 * Get watched for album
	 *
	 * @param (int) $album_id    Album we check
	 * @param bool $user_id
	 * @return
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
	 * Get album watchers
	 * @param $album_id
	 * @return array
	 */
	public function get_album_watchers($album_id)
	{
		$sql = 'SELECT user_id FROM ' . $this->watch_table . ' WHERE album_id = ' . (int) $album_id;
		$result = $this->db->sql_query($sql);
		$watchers = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$watchers[] = $row['user_id'];
		}

		return $watchers;
	}

	/**
	 * Get album watchers
	 * @param $image_id
	 * @return array
	 */
	public function get_image_watchers($image_id)
	{
		$sql = 'SELECT user_id FROM ' . $this->watch_table . ' WHERE image_id = ' . (int) $image_id;
		$result = $this->db->sql_query($sql);
		$watchers = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$watchers[] = $row['user_id'];
		}

		return $watchers;
	}

	/**
	 * Add albums to watch-list
	 *
	 * @param    mixed $album_ids Array or integer with album_id where we delete from the watch-list.
	 * @param bool|int $user_id If not set, it uses the currents user_id
	 */
	public function add_albums($album_ids, $user_id = false)
	{
		$album_ids = $this->cast_mixed_int2array($album_ids);
		$user_id = (int) (($user_id) ? $user_id : $this->user->data['user_id']);

		// First check if we are not subscribed alredy for some
		$sql = 'SELECT * FROM ' . $this->watch_table . ' WHERE user_id = ' . $user_id . ' and ' . $this->db->sql_in_set('album_id', $album_ids);
		$result = $this->db->sql_query($sql);
		$exclude = array();
		while ($row = $this->db->sql_fetchrow($result))
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
	 *
	 * @param (mixed) $ids
	 * @return array
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

	/**
	 *
	 * New image in album
	 * @param $data
	 */
	public function new_image($data)
	{
		$get_watchers = $this->get_album_watchers($data['album_id']);
		// let's exclude all users that are uploadoing something and are approved
		$targets = array_diff($get_watchers, $data['targets']);

		$data['targets'] = $targets;
		$this->notify('new_image', $data);
	}
}
