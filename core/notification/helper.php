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
	\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\album\loader $album_load, \phpbb\controller\helper $helper,
	Container $phpbb_container, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->gallery_auth = $gallery_auth;
		$this->album_load = $album_load;
		$this->helper = $helper;
		$this->phpbb_container = $phpbb_container;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
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
				$album_data = $this->album_load->get($target['album_id']);
				$notification_data = array(
					'user_ids' => $targets,
					'album_id' => $target['album_id'],
					'album_name' => $album_data['album_name'],
					'last_image_id'	=> $target['last_image'],
					'uploader'	=> $target['uploader'],
					'album_url'	=> $this->helper->route('phpbbgallery_album', array('album_id' => $target['album_id'])),
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
}