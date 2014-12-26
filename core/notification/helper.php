<?php
/**
*
* @package phpBB Gallery Extension
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace phpbbgallery\core\notification;

class helper
{
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, Container $phpbb_container, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
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
	public function notify($type, $notify_user, $action_user)
	{
		$notification_data = array(
			'user_id'	=> (int) $notify_user,
			'requester_id'	=> (int) $action_user,
		);
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
		switch ($type)
		{
			case 'add':
				$phpbb_notifications->add_notifications('notification.type.zebraadd', $notification_data);
			break;
			case 'confirm':
				$phpbb_notifications->add_notifications('notification.type.zebraconfirm', $notification_data);
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