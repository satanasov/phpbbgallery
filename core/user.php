<?php

/**
 *
 * @package phpBB Gallery
 * @copyright (c) 2014 nickvergessen
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace phpbbgallery\core;

class user
{
	/**
	 * phpBB-user_id
	 * @var int
	 */
	public $user_id;

	/**
	 * Database object
	 * @var \phpbb\db\driver\driver
	 */
	protected $db;

	/**
	 * Event dispatcher object
	 * @var \phpbb\event\dispatcher
	 */
	protected $dispatcher;

	/**
	 * Gallery users table
	 * @var string
	 */
	protected $table_name;

	/**
	 * Do we have an entry for the user in the table?
	 */
	public $entry_exists = null;

	/**
	 * Users data in the table
	 */
	protected $data = array();

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db Database object
	 * @param    \phpbb\event\dispatcher $dispatcher Event dispatcher object
	 * @param \phpbb\user $user
	 * @param \phpbb\config\config $config
	 * @param \phpbb\auth\auth $auth
	 * @param    string $table_name Gallery users table
	 * @param $root_path
	 * @param $php_ext
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\event\dispatcher $dispatcher, \phpbb\user $user, \phpbb\config\config $config,
	\phpbb\auth\auth $auth,
								$table_name, $root_path, $php_ext)
	{
		$this->db			= $db;
		$this->dispatcher	= $dispatcher;
		$this->user = $user;
		$this->config = $config;
		$this->auth = $auth;
		$this->table_name	= $table_name;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Set the user ID
	 *
	 * @param	int		$user_id
	 * @param	bool	$load		Shall we automatically load the users data from the database?
	 */
	public function set_user_id($user_id, $load = true)
	{
		$this->user_id		= (int) $user_id;
		if ($load)
		{
			$this->load_data();
		}
	}

	/**
	 * Is it the same user?
	 *
	 * @param	int		$user_id
	 * @return	bool
	 */
	public function is_user($user_id)
	{
		return $this->user_id == $user_id;
	}

	/**
	* Load the users data from the database and cast it...
	*/
	public function load_data()
	{
		$this->entry_exists	= false;
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE user_id = ' . $this->user_id;
		$result = $this->db->sql_query($sql, 30);
		if ($row = $this->db->sql_fetchrow($result))
		{
			$this->data			= $this->validate_data($row);
			$this->entry_exists	= true;
		}
		$this->db->sql_freeresult($result);

	}

	/**
	 * Load the users data from the database and cast it...
	 * @param $time
	 */
	public function set_permissions_changed($time)
	{
		if ($this->data)
		{
			$this->data['user_permissions_changed'] = $time;
		}
	}

	/**
	* Some functions need the data to be loaded or at least checked.
	* So here we loaded if it is not laoded yet and we need it ;)
	*/
	public function force_load()
	{
		if (is_null($this->entry_exists))
		{
			$this->load_data();
		}
	}

	/**
	* Get user-setting, if the user does not have his own settings we fall back to default.
	*
	* @param	string	$key		Column name from the users-table
	* @param	bool	$default	Load default value, if user has no entry
	* @return	mixed			Returns the value of the column, it it does not exist it returns false.
	*/
	public function get_data($key, $default = true)
	{
		if (isset($this->data[$key]))
		{
			return $this->data[$key];
		}
		else if ($default && $this->get_default_value($key) !== null)
		{
			return $this->get_default_value($key);
		}

		return false;
	}

	/**
	 * Updates/Inserts the data, depending on whether the user already exists or not.
	 *    Example: 'SET key = x'
	 * @param $data
	 * @return bool
	 */
	public function update_data($data)
	{
		$this->force_load();

		$suc = false;
		if ($this->entry_exists)
		{
			$suc = $this->update($data);
		}

		if (($suc === false) || !$this->entry_exists)
		{
			$suc = $this->insert($data);
		}

		return $suc;
	}

	/**
	 * Increase/Inserts the data, depending on whether the user already exists or not.
	 *    Example: 'SET key = key + x'
	 * @param $num
	 * @return bool
	 */
	public function update_images($num)
	{
		$suc = false;
		if ($this->entry_exists || is_null($this->entry_exists))
		{
			$suc = $this->update_image_count($num);
			if ($suc === false)
			{
				$suc = $this->update(array('user_images' => max(0, $num)));
			}
		}

		if ($suc === false)
		{
			$suc = $this->insert(array('user_images' => max(0, $num)));
		}

		return $suc;
	}

	/**
	* Updates the users table with the new data.
	*
	* @param	array	$data	Array of data we want to add/update.
	* @return	bool			Returns true if the columns were updated successfully
	*/
	private function update($data)
	{
		$sql_ary = array_merge($this->validate_data($data), array(
			'user_last_update'	=> time(),
		));
		unset($sql_ary['user_id']);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE user_id = ' . $this->user_id;
		$this->db->sql_query($sql);

		$this->data = array_merge($this->data, $sql_ary);

		return ($this->db->sql_affectedrows() == 1) ? true : false;
	}

	/**
	* Updates the users table by increasing the values.
	*
	* @param	int		$num	Number of images to add to the counter
	* @return	bool			Returns true if the columns were updated successfully, else false
	*/
	protected function update_image_count($num)
	{
		$sql = 'UPDATE ' . $this->table_name . '
			SET user_images = user_images ' . (($num > 0) ? (' + ' . $num) : (' - ' . abs($num))) . ',
				user_last_update = ' . time() . '
			WHERE ' . (($num < 0) ? ' user_images > ' . abs($num) . ' AND ' : '') . '
				user_id = ' . $this->user_id;
		$this->db->sql_query($sql);

		if ($this->db->sql_affectedrows() == 1)
		{
			if (!empty($this->data))
			{
				$this->data['user_last_update'] = time();
				$this->data['user_images'] += $num;
			}
			return true;
		}
		return false;
	}

	/**
	* Updates the users table with the new data.
	*
	* @param	array	$data	Array of data we want to insert
	* @return	bool			Returns true if the data was inserted successfully
	*/
	private function insert($data)
	{
		$sql_ary = array_merge(self::get_default_values(), $this->validate_data($data), array(
			'user_id'			=> $this->user_id,
			'user_last_update'	=> time(),
		));

		$this->db->sql_return_on_error(true);

		$sql = 'INSERT INTO ' . $this->table_name . '
			' . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);
		$error = $this->db->get_sql_error_triggered();

		$this->db->sql_return_on_error(false);

		$this->data = $sql_ary;
		$this->entry_exists = true;

		return ($error) ? false : true;
	}

	/**
	* Delete the user from the table.
	*/
	public function delete()
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE user_id = ' . $this->user_id;
		$this->db->sql_query($sql);
	}

	/**
	* Delete the user from the table.
	*
	* @param	mixed	$user_ids	Can either be an array of IDs, one ID or the string 'all' to delete all users.
	*/
	public function delete_users($user_ids)
	{

		$sql_where = $this->sql_build_where($user_ids);

		$sql = 'DELETE FROM ' . $this->table_name . '
			' . $sql_where;
		$this->db->sql_query($sql);
	}

	/**
	* Updates the users table with new data.
	*
	* @param	mixed	$user_ids	Can either be an array of IDs, one ID or the string 'all' to update all users.
	* @param	array	$data		Array of data we want to add/update.
	* @return	bool				Returns true if the columns were updated successfully
	*/
	public function update_users($user_ids, $data)
	{
		$sql_ary = array_merge($this->validate_data($data), array(
			'user_last_update'	=> time(),
		));
		unset($sql_ary['user_id']);

		$sql_where = $this->sql_build_where($user_ids);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			' . $sql_where;
		$this->db->sql_query($sql);

		return ($this->db->sql_affectedrows() != 0) ? true : false;
	}

	/**
	* Builds a valid WHERE-sql-statement, with casted integers, or empty to allow handling all users.
	*
	* @param	mixed	$user_ids	Can either be an array of IDs, one ID or the string 'all' to update all users.
	* @return	string				The WHERE statement with "WHERE " if needed.
	*/
	public function sql_build_where($user_ids)
	{
		if (is_array($user_ids) && !empty($user_ids))
		{
			$sql_where = 'WHERE ' . $this->db->sql_in_set('user_id', array_map('intval', $user_ids));
		}
		else if ($user_ids == 'all')
		{
			$sql_where = '';
		}
		else
		{
			$sql_where = 'WHERE user_id = ' . (int) $user_ids;
		}

		return $sql_where;
	}

	/**
	* Validate user data.
	*
	* @param	array	$data	Array of data we need to validate
	* @param	bool	$inc	Are we incrementing the value
	* @return	array			Array with all allowed keys and their casted and selected values
	*/
	public function validate_data($data, $inc = false)
	{
		$validated_data = array();
		foreach ($data as $name => $value)
		{
			switch ($name)
			{
				case 'user_id':
				case 'user_images':
				case 'personal_album_id':
				case 'user_lastmark':
				case 'user_last_update':
					if ($inc && ($name == 'user_images'))
					{
						// While incrementing, the iamges might be lower than 0.
						$validated_data[$name] = (int) $value;
					}
					else
					{
						$validated_data[$name] = max(0, (int) $value);
					}
				break;

				case 'watch_own':
				case 'watch_com':
				case 'subscribe_pegas':
					$validated_data[$name] = (bool) $value;
				break;

				case 'user_permissions':
					$validated_data[$name] = $value;
				break;

				default:
					$is_validated = false;

					/**
					* Event user validat data
					*
					* @event phpbbgallery.core.user.validate_data
					* @var	bool	is_validated	is value validated
					* @var	string	name			value name
					* @var	mixed	value			value of the value
					* @since 1.2.0
					*/
					$vars = array('is_validated', 'name', 'value');
					extract($this->dispatcher->trigger_event('phpbbgallery.core.user.validate_data', compact($vars)));

					if ($is_validated)
					{
						$validated_data[$name] = $value;
					}
				break;
			}
		}
		return $validated_data;
	}

	private function get_default_value($key)
	{
		$default_values = $this->get_default_values();

		if (isset($default_values[$key]))
		{
			return $default_values[$key];
		}

		return null;
	}

	private function get_default_values()
	{
		static $default_values;

		if ($default_values)
		{
			return $default_values;
		}

		$default_values = self::$default_values;

		/**
		* Event user validat data
		*
		* @event phpbbgallery.core.user.get_default_values
		* @var	array	default_values	the default values array
		* @since 1.2.0
		*/
		$vars = array('default_values');
		extract($this->dispatcher->trigger_event('phpbbgallery.core.user.get_default_values', compact($vars)));

		return $default_values;
	}

	/**
	* Default values for new users.
	*/
	static protected $default_values = array(
		'user_images'		=> 0,
		'personal_album_id'	=> 0,
		'user_lastmark'		=> 0,
		'user_last_update'	=> 0,
		'user_permissions_changed'	=> 0,

		'user_permissions'	=> '',

		// Shall other users be allowed to comment on this users images by default?
		'user_allow_comments'	=> true,
		// Shall the user be subscribed to his own images?
		'watch_own'			=> true,
		// Shall the user be subscribed if he comments on an images?
		'watch_com'			=> false,
		// Automatically subscribe user to new personal galleries?
		'subscribe_pegas'	=> false,
	);

	/**
	 * @param $user_cache
	 * @param $row
	 */
	public function add_user_to_cache(&$user_cache, $row)
	{
		$user_id = $row['user_id'];
		if (!function_exists('phpbb_get_user_avatar'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}

		if ($user_id == ANONYMOUS)
		{
			$user_cache[$user_id] = array(
				'username'			=> $row['username'],
				'user_colour'		=> $row['user_colour'],
				'joined'		=> '',
				'posts'			=> '',
				'warnings'			=> 0,
				'allow_pm'			=> 0,

				'sig'					=> '',
				'sig_bbcode_uid'		=> '',
				'sig_bbcode_bitfield'	=> '',

				'online'			=> false,
				'avatar'			=> ($this->user->optionget('viewavatars')) ? phpbb_get_user_avatar($row) : '',
				'rank_title'		=> '',
				'rank_image'		=> '',
				'rank_image_src'	=> '',
				'profile'			=> '',
				'pm'				=> '',
				'email'				=> '',
				'jabber'			=> '',
				'search'			=> '',
				'age'				=> '',

				'gallery_album'		=> '',
				'gallery_images'	=> '',
				'gallery_search'	=> '',

			);

			get_user_rank($row['user_rank'], false, $user_cache[$user_id]['rank_title'], $user_cache[$user_id]['rank_image'], $user_cache[$user_id]['rank_image_src']);
		}
		else
		{
			$user_sig = '';
			if ($row['user_sig'] && $this->config['allow_sig'] && $this->user->optionget('viewsigs'))
			{
				$user_sig = $row['user_sig'];
			}

			$id_cache[] = $user_id;

			$user_cache[$user_id] = array(
				'joined'		=> $this->user->format_date($row['user_regdate']),
				'posts'			=> $row['user_posts'],
				'warnings'		=> (isset($row['user_warnings'])) ? $row['user_warnings'] : 0,
				'viewonline'	=> $row['user_allow_viewonline'],
				'allow_pm'		=> $row['user_allow_pm'],

				'sig'					=> $user_sig,
				'sig_bbcode_uid'		=> (!empty($row['user_sig_bbcode_uid'])) ? $row['user_sig_bbcode_uid'] : '',
				'sig_bbcode_bitfield'	=> (!empty($row['user_sig_bbcode_bitfield'])) ? $row['user_sig_bbcode_bitfield'] : '',

				'avatar'		=> ($this->user->optionget('viewavatars')) ? phpbb_get_user_avatar($row) : '',
				'age'			=> '',

				'rank_title'		=> '',
				'rank_image'		=> '',
				'rank_image_src'	=> '',

				'user_id'			=> $row['user_id'],
				'username'			=> $row['username'],
				'user_colour'		=> $row['user_colour'],

				'online'		=> false,
				'profile'		=> append_sid($this->root_path . 'memberlist.' . $this->php_ext, "mode=viewprofile&amp;u=$user_id"),
				'jabber'		=> ($row['user_jabber'] && $this->auth->acl_get('u_sendim')) ? append_sid($this->root_path . 'memberlist.' . $this->php_ext, "mode=contact&amp;action=jabber&amp;u=$user_id") : '',
				'search'		=> ($this->auth->acl_get('u_search')) ? append_sid($this->root_path . 'search.' . $this->php_ext, "author_id=$user_id&amp;sr=posts") : '',

				'gallery_album'		=> '',//($row['personal_album_id'] && $config['phpbb_gallery_viewtopic_icon']) ? $phpbb_ext_gallery->url->append_sid('album', "album_id=" . $row['personal_album_id']) : '',
				'gallery_images'	=> ($this->config['phpbb_gallery_viewtopic_images']) ? $row['user_images'] : 0,
				'gallery_search'	=> '',//($config['phpbb_gallery_viewtopic_images'] && $config['phpbb_gallery_viewtopic_images'] && $row['user_images']) ? $phpbb_ext_gallery->url->append_sid('search', "user_id=$user_id") : '',
			);

			get_user_rank($row['user_rank'], $row['user_posts'], $user_cache[$user_id]['rank_title'], $user_cache[$user_id]['rank_image'], $user_cache[$user_id]['rank_image_src']);

			if (!empty($row['user_allow_viewemail']) || $this->auth->acl_get('a_email'))
			{
				$user_cache[$user_id]['email'] = ($this->config['board_email_form'] && $this->config['email_enable']) ? append_sid($this->root_path . 'memberlist.' . $this->php_ext, "mode=email&amp;u=$user_id") : (($this->config['board_hide_emails'] && !$this->auth->acl_get('a_email')) ? '' : 'mailto:' . $row['user_email']);
			}
			else
			{
				$user_cache[$user_id]['email'] = '';
			}

			if ($this->config['allow_birthdays'] && !empty($row['user_birthday']))
			{
				list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $row['user_birthday']));
				$age = 0;
				if ($bday_year)
				{
					$now = $this->user->create_datetime();
					$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());

					$diff = $now['mon'] - $bday_month;
					if ($diff == 0)
					{
						$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
					}
					else
					{
						$diff = ($diff < 0) ? 1 : 0;
					}
					$age = max(0, (int) ($now['year'] - $bday_year - $diff));
				}

				$user_cache[$user_id]['age'] = $age;
			}
		}
	}

	/**
	* Get user personal album
	* Checks and returns users personal album
	* returns (int) $album_id or 0
	*/
	public function get_own_root_album()
	{
		$sql = 'SELECT personal_album_id FROM ' . $this->table_name . ' WHERE user_id = ' . $this->user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return (int) $row['personal_album_id'];
	}

	/**
	* Destroy user data and set this class to empty
	*/
	public function destroy()
	{
		$this->user_id = null;
		$this->entry_exists = null;
		$this->data = array();
	}
}
