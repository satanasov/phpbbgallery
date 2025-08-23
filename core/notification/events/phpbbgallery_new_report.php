<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\core\notification\events;

class phpbbgallery_new_report extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'phpbbgallery.core.notification.new_report';
	}
	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_PHPBBGALLERY_NEW_REPORT',
	);

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var \phpbb\config\config */
	protected $config;

	public function set_config(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

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
		return (int) $data['item_id'];
	}

	/**
	 * Get the id of the parent
	 *
	 * @param array $data The data for the updated rules
	 * @return int
	 */
	public static function get_item_parent_id($data)
	{
		// No parent
		return 0;
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
	 * Get the user's avatar
	 */
	public function get_avatar()
	{
		$users = array($this->get_data('reporter'));
		$this->user_loader->load_users($users);
		return $this->user_loader->get_avatar($this->get_data('reporter'));
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$users = array($this->get_data('reporter'));
		$this->user_loader->load_users($users);
		$username = $this->user_loader->get_username($this->get_data('reporter'), 'no_profile');
		return $this->language->lang('NOTIFICATION_PHPBBGALLERY_NEW_REPORT', $username);
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
	 * Users needed to query before this notification can be displayed
	 *
	 * @return array Array of user_ids
	 */
	public function users_to_query()
	{
		return array();
	}

	/**
	 * Get the url to this item
	 *
	 * @return string URL
	 */
	public function get_url()
	{
		return $this->get_data('reported_image_id') ? append_sid($this->phpbb_root_path . 'gallery/moderate/image/' . $this->get_data('reported_image_id')) : $this->get_data('url');
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
		$this->set_data('item_id', $data['item_id']);
		$this->set_data('reporter', $data['reporter']);
		$this->set_data('reported_image_id', $data['reported_image_id']);
		$this->set_data('url', $data['url']);
		parent::create_insert_array($data, $pre_create_data);
	}
}
