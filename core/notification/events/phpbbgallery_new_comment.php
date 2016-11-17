<?php
/**
*
* @package phpBB Gallery Extension
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace phpbbgallery\core\notification\events;

class phpbbgallery_new_comment extends \phpbb\notification\type\base
{
	protected $helper;

	public function __construct(\phpbb\user_loader $user_loader, \phpbb\db\driver\driver_interface $db, \phpbb\cache\driver\driver_interface $cache,
	\phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper,
	$phpbb_root_path, $php_ext, $notification_types_table, $notifications_table, $user_notifications_table)
	{
		$this->user_loader = $user_loader;
		$this->db = $db;
		$this->cache = $cache;
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->helper = $helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->notification_types_table = $notification_types_table;
		$this->notifications_table = $notifications_table;
		$this->user_notifications_table = $user_notifications_table;
	}
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'phpbbgallery.core.notification.new_comment';
	}
	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_COMMENT',
	);
	/**
	* Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	*
	* @return bool True/False whether or not this is available to the user
	*/
	public function is_available()
	{
		return true;
	}

	/**
	 * Get the id of the rule
	 *
	 * @param array $data The data for the updated rules
	 * @return mixed
	 */
	public static function get_item_id($data)
	{
		return $data['comment_id'];
	}

	/**
	 * Get the id of the parent
	 *
	 * @param array $data The data for the updated rules
	 * @return mixed
	 */
	public static function get_item_parent_id($data)
	{
		// No parent
		return $data['image_id'];
	}

	/**
	 * Find the users who will receive notifications
	 *
	 * @param array $data The data for the updated rules
	 *
	 * @param array $options
	 * @return array
	 */
	public function find_users_for_notification($data, $options = array())
	{
		$this->user_loader->load_users($data['user_ids']);
		return $this->check_user_notification_options($data['user_ids'], $options);
	}
	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array();
	}
	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		$users = array($this->get_data('poster_id'));
		$this->user_loader->load_users($users);
		return $this->user_loader->get_avatar($this->get_data('poster_id'));
	}
	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$users = array($this->get_data('poster_id'));
		$this->user_loader->load_users($users);
		$username = $this->user_loader->get_username($this->get_data('poster_id'), 'no_profile');
		return $this->user->lang('NOTIFICATION_PHPBBGALLERY_NEW_COMMENT', $username);
	}
	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return $this->get_data('url');
	}
	/**
	* Get email template
	*
	* @return string|bool
	*/
	public function get_email_template()
	{
		return false;
	}
	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
		return array();
	}
	/**
	* Function for preparing the data for insertion in an SQL query
	* (The service handles insertion)
	*
	* @param array $data The data for the updated rules
	* @param array $pre_create_data Data from pre_create_insert_array()
	*
	* @return array Array of data ready to be inserted into the database
	*/
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('image_id', $data['image_id']);
		$this->set_data('comment_id', $data['comment_id']);
		$this->set_data('poster_id', $data['poster']);
		$this->set_data('url', $data['url']);
		return parent::create_insert_array($data, $pre_create_data);
	}
}
