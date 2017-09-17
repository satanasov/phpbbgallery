<?php

/**
*
* @package phpBB Gallery
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\auth;

class auth
{
	const SETTING_PERMISSIONS	= -39839;
	const PERSONAL_ALBUM		= -3;
	const OWN_ALBUM				= -2;
	const PUBLIC_ALBUM			= 0;

	const ACCESS_ALL			= 0;
	const ACCESS_REGISTERED		= 1;
	const ACCESS_NOT_FOES		= 2;
	const ACCESS_FRIENDS		= 3;
	const ACCESS_SPECIAL_FRIENDS	= 4;

	// ACL - slightly different
	const ACL_NO		= 0;
	const ACL_YES		= 1;
	const ACL_NEVER		= 2;

	static protected $_permission_i = array('i_view', 'i_watermark', 'i_upload', 'i_approve', 'i_edit', 'i_delete', 'i_report', 'i_rate');
	static protected $_permission_c = array('c_read', 'c_post', 'c_edit', 'c_delete');
	static protected $_permission_m = array('m_comments', 'm_delete', 'm_edit', 'm_move', 'm_report', 'm_status');
	static protected $_permission_misc = array('a_list', 'i_count', 'i_unlimited', 'a_count', 'a_unlimited', 'a_restrict');
	static protected $_permissions = array();
	static protected $_permissions_flipped = array();

	protected $_auth_data = array();
	protected $_auth_data_never = array();

	protected $acl_cache = array();

	/**
	* Cache object
	* @var \phpbbgallery\core\cache
	*/
	protected $cache;

	/**
	* Database object
	* @var \phpbb\db\driver\driver
	*/
	protected $db;

	/**
	* Gallery user object
	* @var \phpbbgallery\core\user
	*/
	protected $user;

	/**
	* Gallery permissions table
	* @var string
	*/
	protected $table_permissions;

	/**
	* Gallery permission roles table
	* @var string
	*/
	protected $table_roles;

	/**
	* Gallery users table
	* @var string
	*/
	protected $table_users;

	/**
	 * Construct
	 *
	 * @param    \phpbbgallery\core\cache $cache Cache object
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db Database object
	 * @param    \phpbbgallery\core\user $user Gallery user object
	 * @param \phpbb\user $phpbb_user
	 * @param \phpbb\auth\auth $auth
	 * @param    string $permissions_table Gallery permissions table
	 * @param    string $roles_table Gallery permission roles table
	 * @param    string $users_table Gallery users table
	 * @param $albums_table
	 */
	public function __construct(\phpbbgallery\core\cache $cache, \phpbb\db\driver\driver_interface $db, \phpbbgallery\core\user $user, \phpbb\user $phpbb_user, \phpbb\auth\auth $auth,
	$permissions_table, $roles_table, $users_table, $albums_table)
	{
		$this->cache = $cache;
		$this->db = $db;
		$this->user = $user;
		$this->phpbb_user = $phpbb_user;
		$this->auth = $auth;
		$this->table_permissions = $permissions_table;
		$this->table_roles = $roles_table;
		$this->table_users = $users_table;
		$this->table_albums = $albums_table;

		self::$_permissions = array_merge(self::$_permission_i, self::$_permission_c, self::$_permission_m, self::$_permission_misc);
		self::$_permissions_flipped = array_flip(array_merge(self::$_permissions, array('m_')));
		self::$_permissions_flipped['i_count'] = 'i_count';
		self::$_permissions_flipped['a_count'] = 'a_count';
	}

	public function get_setting_permissions()
	{
		return self::SETTING_PERMISSIONS;
	}

	public function get_personal_album()
	{
		return self::PERSONAL_ALBUM;
	}

	public function get_own_album()
	{
		return self::OWN_ALBUM;
	}

	public function load_user_premissions($user_id, $album_id = false)
	{
		$cached_permissions = $this->user->get_data('user_permissions');
		if (($user_id == $this->user->user_id) && !empty($cached_permissions))
		{
			$this->unserialize_auth_data($cached_permissions);
			return;
		}

		else if ($user_id != $this->user->user_id)
		{
			$this->user->set_user_id($user_id);
			$cached_permissions = $this->user->get_data('user_permissions');
			if (!empty($cached_permissions))
			{
				$this->unserialize_auth_data($cached_permissions);
				return;
			}
		}
		/*else {
			$this->user->set_user_id($user_id);
			$cached_permissions = $this->user->get_data('user_permissions');
			$this->unserialize_auth_data($cached_permissions);
			return;
		}*/
		$this->query_auth_data($user_id);
	}

	/**
	 * Query the permissions for a given user and store them in the database.
	 * @param $user_id
	 */
	protected function query_auth_data($user_id)
	{
		//$albums = array();//@todo $this->cache->obtain_album_list();
		$albums = $this->cache->get('albums');
		$user_groups_ary = self::get_usergroups($user_id);

		$sql_select = '';
		foreach (self::$_permissions as $permission)
		{
			$sql_select .= " MAX($permission) as $permission,";
		}

		$this->_auth_data[self::OWN_ALBUM]				= new \phpbbgallery\core\auth\set();
		$this->_auth_data_never[self::OWN_ALBUM]		= new \phpbbgallery\core\auth\set();
		$this->_auth_data[self::PERSONAL_ALBUM]			= new \phpbbgallery\core\auth\set();
		$this->_auth_data_never[self::PERSONAL_ALBUM]	= new \phpbbgallery\core\auth\set();

		foreach ($albums as $album)
		{
			if ($album['album_user_id'] == self::PUBLIC_ALBUM)
			{
				$this->_auth_data[$album['album_id']]		= new \phpbbgallery\core\auth\set();
				$this->_auth_data_never[$album['album_id']]	= new \phpbbgallery\core\auth\set();
			}
		}

		$sql_array = array(
			'SELECT'		=> "p.perm_album_id, $sql_select p.perm_system",
			'FROM'			=> array($this->table_permissions => 'p'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array($this->table_roles => 'pr'),
					'ON'		=> 'p.perm_role_id = pr.role_id',
				),
			),

			'WHERE'			=> 'p.perm_user_id = ' . $user_id . ' OR ' . $this->db->sql_in_set('p.perm_group_id', $user_groups_ary, false, true),
			'GROUP_BY'		=> 'p.perm_system, p.perm_album_id',
			'ORDER_BY'		=> 'p.perm_system DESC, p.perm_album_id ASC',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$this->db->sql_return_on_error(true);
		$result = $this->db->sql_query($sql);
		if ($this->db->get_sql_error_triggered())
		{
			trigger_error('DATABASE_NOT_UPTODATE');

		}
		$this->db->sql_return_on_error(false);

		while ($row = $this->db->sql_fetchrow($result))
		{
			switch ($row['perm_system'])
			{
				case self::PERSONAL_ALBUM:
					$this->store_acl_row(self::PERSONAL_ALBUM, $row);
				break;

				case self::OWN_ALBUM:
					$this->store_acl_row(self::OWN_ALBUM, $row);
				break;

				case self::PUBLIC_ALBUM:
					$this->store_acl_row(((int) $row['perm_album_id']), $row);
				break;
			}
		}
		$this->db->sql_freeresult($result);

		$this->merge_acl_row();

		$this->restrict_pegas($user_id);

		$this->set_user_permissions($user_id, $this->_auth_data);
	}

	/**
	 * Serialize the auth-data sop we can store it.
	 *
	 * Line-Format:    bitfields:i_count:a_count::album_id(s)
	 * Samples:        8912837:0:10::-3
	 *                9961469:20:0::1:23:42
	 * @param $auth_data
	 * @return string
	 */
	protected function serialize_auth_data($auth_data)
	{
		$acl_array = array();

		foreach ($auth_data as $a_id => $obj)
		{
			$key = $obj->get_bits() . ':' . $obj->get_count('i_count') . ':' . $obj->get_count('a_count');
			if (!isset($acl_array[$key]))
			{
				$acl_array[$key] = $key . '::' . $a_id;
			}
			else
			{
				$acl_array[$key] .= ':' . $a_id;
			}
		}

		return implode("\n", $acl_array);
	}

	/**
	 * Unserialize the stored auth-data
	 * @param $serialized_data
	 */
	protected function unserialize_auth_data($serialized_data)
	{
		$acl_array = explode("\n", $serialized_data);

		foreach ($acl_array as $acl_row)
		{
			list ($acls, $a_ids) = explode('::', $acl_row);
			list ($bits, $i_count, $a_count) = explode(':', $acls);

			foreach (explode(':', $a_ids) as $a_id)
			{
				$this->_auth_data[$a_id] = new \phpbbgallery\core\auth\set($bits, $i_count, $a_count);
			}
		}
	}

	/**
	 * Stores an acl-row into the _auth_data-array.
	 * @param $album_id
	 * @param $data
	 */
	protected function store_acl_row($album_id, $data)
	{
		if (!isset($this->_auth_data[$album_id]))
		{
			// The album we have permissions for does not exist any more, so do nothing.
			return;
		}

		foreach (self::$_permissions as $permission)
		{
			if (strpos($permission, '_count') === false)
			{
				if ($data[$permission] == self::ACL_NEVER)
				{
					$this->_auth_data_never[$album_id]->set_bit(self::$_permissions_flipped[$permission], true);
				}
				else if ($data[$permission] == self::ACL_YES)
				{
					$this->_auth_data[$album_id]->set_bit(self::$_permissions_flipped[$permission], true);
					if (substr($permission, 0, 2) == 'm_')
					{
						$this->_auth_data[$album_id]->set_bit(self::$_permissions_flipped['m_'], true);
					}
				}
			}
			else
			{
				$this->_auth_data[$album_id]->set_count($permission, $data[$permission]);
			}
		}
	}

	/**
	* Merge the NEVER-options into the YES-options by removing the YES, if it is set.
	*/
	protected function merge_acl_row()
	{
		foreach ($this->_auth_data as $album_id => $obj)
		{
			foreach (self::$_permissions as $acl)
			{
				if (strpos('_count', $acl) === false)
				{
					$bit = self::$_permissions_flipped[$acl];
					// If the yes and the never bit are set, we overwrite the yes with a false.
					if ($obj->get_bit($bit) && $this->_auth_data_never[$album_id]->get_bit($bit))
					{
						$obj->set_bit($bit, false);
					}
				}
			}
		}
	}

	/**
	 * Restrict the access to personal galleries, if the user is not a moderator.
	 * @param $user_id
	 */
	protected function restrict_pegas($user_id)
	{
		if (($user_id != ANONYMOUS) && $this->_auth_data[self::PERSONAL_ALBUM]->get_bit(self::$_permissions_flipped['m_']))
		{
			// No restrictions for moderators.
			return;
		}

		$zebra = null;

		$albums = array();//@todo $this->cache->obtain_album_list();
		foreach ($albums as $album)
		{
			if (!$album['album_auth_access'] || ($album['album_user_id'] == self::PUBLIC_ALBUM))# || ($album['album_user_id'] == $user_id))
			{
				continue;
			}
			else if ($user_id == ANONYMOUS)
			{
				// Level 1: No guests
				$this->_auth_data[$album['album_id']] = new \phpbbgallery\core\auth\set();
				continue;
			}
			else if ($album['album_auth_access'] == self::ACCESS_NOT_FOES)
			{
				if ($zebra == null)
				{
					$zebra = self::get_user_zebra($user_id);
				}
				if (in_array($album['album_user_id'], $zebra['foe']))
				{
					// Level 2: No foes allowed
					$this->_auth_data[$album['album_id']] = new \phpbbgallery\core\auth\set();
					continue;
				}
			}
			else if ($album['album_auth_access'] == self::ACCESS_SPECIAL_FRIENDS)
			{
				if ($zebra == null)
				{
					$zebra = self::get_user_zebra($user_id);
				}
				if (!in_array($album['album_user_id'], $zebra['bff']))
				{
					// Level 4: Only special friends allowed
					$this->_auth_data[$album['album_id']] = new \phpbbgallery\core\auth\set();
					continue;
				}
			}
			else if ($album['album_auth_access'] == self::ACCESS_FRIENDS)
			{
				if ($zebra == null)
				{
					$zebra = self::get_user_zebra($user_id);
				}
				if (!in_array($album['album_user_id'], $zebra['friend']))
				{
					// Level 3: Only friends allowed
					$this->_auth_data[$album['album_id']] = new \phpbbgallery\core\auth\set();
					continue;
				}
			}
		}
	}

	/**
	 * Get the users, which added our user as friend and/or foe
	 * @param $user_id
	 * @return array
	 */
	public function get_user_zebra($user_id)
	{

		$zebra = array('foe' => array(), 'friend' => array(), 'bff' => array());
		$sql = 'SELECT *
			FROM ' . ZEBRA_TABLE . '
			WHERE zebra_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['foe'])
			{
				$zebra['foe'][] = (int) $row['user_id'];
			}
			else
			{
				if (isset($row['bff']))
				{
					if ($row['bff'])
					{
						$zebra['bff'][] = (int) $row['user_id'];
					}
					else
					{
						$zebra['friend'][] = (int) $row['user_id'];
					}
				}
				else
				{
					$zebra['friend'][] = (int) $row['user_id'];
				}
			}
		}
		$this->db->sql_freeresult($result);
		return $zebra;
	}

	/**
	 * Get zebra state
	 * @param $zebra_array
	 * @param $album_author
	 * @param $album_id
	 * @return int
	 */
	public function get_zebra_state($zebra_array, $album_author, $album_id)
	{
		$state = 0;
		// if we check for ourselves or user is mod or admin - make bigest possible step
		if ($this->phpbb_user->data['user_id'] == $album_author || $this->acl_check('m_', $album_author, $album_id) || $this->auth->acl_get('a_user'))
		{
			$state = 5;
		}
		//If user is not anon - we will check ... else its state is 0
		else if ($this->phpbb_user->data['user_id'] != ANONYMOUS)
		{
			if (in_array($album_author, $zebra_array['foe']))
			{
				$state = 1;
			}
			else if (in_array($album_author, $zebra_array['friend']))
			{
				$state = 3;
			}
			else if (in_array($album_author, $zebra_array['bff']))
			{
				$state = 4;
			}
			else
			{
				$state = 2;
			}
		}
		return $state;
	}

	/**
	 * Get groups a user is member from.
	 * @param $user_id
	 * @return array
	 */
	public function get_usergroups($user_id)
	{
		$groups_ary = array();

		$sql = 'SELECT ug.group_id
			FROM ' . USER_GROUP_TABLE . ' ug
			LEFT JOIN ' . GROUPS_TABLE . ' g
				ON (ug.group_id = g.group_id)
			WHERE ug.user_id = ' . (int) $user_id . '
				AND ug.user_pending = 0
				AND g.group_skip_auth = 0';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$groups_ary[] = $row['group_id'];
		}
		$this->db->sql_freeresult($result);

		return $groups_ary;
	}

	/**
	 * Sets the permissions-cache in users-table to given array.
	 * @param $user_ids
	 * @param bool $permissions
	 */
	public function set_user_permissions($user_ids, $permissions = false)
	{
		$sql_set = (is_array($permissions)) ? $this->db->sql_escape($this->serialize_auth_data($permissions)) : '';
		$sql_where = '';
		if (is_array($user_ids))
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

		if ($this->user->is_user($user_ids))
		{
			$this->user->set_permissions_changed(time());
		}

			$sql = 'UPDATE ' . $this->table_users . "
				SET user_permissions = '" . $sql_set . "',
					user_permissions_changed = " . time() . '
				' . $sql_where;
			$this->db->sql_query($sql);
	}

	/**
	* Get permission
	*
	* @param	string	$acl	One of the permissions, Exp: i_view
	* @param	int		$a_id	The album_id, from which we want to have the permissions
	* @param	int		$u_id	The user_id from the album-owner. If not specified we need to get it from the cache.
	*
	* @return	bool			Is the user allowed to do the $acl?
	*/
	public function acl_check($acl, $a_id, $u_id = -1)
	{
		$bit = self::$_permissions_flipped[$acl];

		if ($bit < 0)
		{
			$bit = $acl;
		}

		if (isset($this->acl_cache[$a_id][$bit]))
		{
			return $this->acl_cache[$a_id][$bit];
		}

		// Do we have a function call without $album_user_id ?
		if (($u_id < self::PUBLIC_ALBUM) && ($a_id > 0))
		{
			static $_album_list;
			// Yes, from viewonline.php
			if (!$_album_list)
			{
				$_album_list = $this->cache->get_albums();
			}
			if (!isset($_album_list[$a_id]))
			{
				// Do not give permissions, if the album does not exist.
				return false;
			}
			$u_id = $_album_list[$a_id]['album_user_id'];
		}

		$get_acl = 'get_bit';
		if (!is_int($bit))
		{
			$get_acl = 'get_count';
		}
		$p_id = $a_id;
		if ($u_id)
		{
			$this->user->set_user_id($this->phpbb_user->data['user_id']);
			if ($this->user->is_user($u_id))
			{
				$p_id = self::OWN_ALBUM;
			}
			else
			{
				if (!isset($this->_auth_data[$a_id]))
				{
					$p_id = self::PERSONAL_ALBUM;
				}
			}
		}

		if (isset($this->_auth_data[$p_id]))
		{
			$this->acl_cache[$a_id][$bit] = $this->_auth_data[$p_id]->$get_acl($bit);
			return $this->acl_cache[$a_id][$bit];
		}
		return false;
	}

	/**
	* Does the user have the permission for any album?
	*
	* @param	string	$acl			One of the permissions, Exp: i_view; *_count permissions are not allowed!
	*
	* @return	bool			Is the user allowed to do the $acl?
	*/
	public function acl_check_global($acl)
	{
		$bit = self::$_permissions_flipped[$acl];
		if (!is_int($bit))
		{
			// No support for *_count permissions.
			return false;
		}

		if ($this->_auth_data[self::OWN_ALBUM]->get_bit($bit))
		{
			return true;
		}
		if ($this->_auth_data[self::PERSONAL_ALBUM]->get_bit($bit))
		{
			return true;
		}

		$albums = $this->cache->get_albums();
		foreach ($albums as $album)
		{
			if (!$album['album_user_id'] && $this->_auth_data[$album['album_id']]->get_bit($bit))
			{
				return true;
			}
		}

		return false;
	}

	/**
	* Get albums by permission
	*
	* @param	string	$acl			One of the permissions, Exp: i_view; *_count permissions are not allowed!
	* @param	string	$return			Type of the return value. array returns an array, else it's a string.
	*									bool means it only checks whether the user has the permission anywhere.
	* @param	bool	$display_in_rrc	Only return albums, that have the display_in_rrc-flag set.
	* @param	bool	$display_pegas	Include personal galleries in the list.
	*
	* @return	mixed					$album_ids, either as list or array.
	*/
	public function acl_album_ids($acl, $return = 'array', $display_in_rrc = false, $display_pegas = true)
	{
		$bit = self::$_permissions_flipped[$acl];
		if (!is_int($bit))
		{
			// No support for *_count permissions.
			return ($return == 'array') ? array() : '';
		}

		$album_list = '';
		$album_array = array();
		$albums = $this->cache->get_albums();
		foreach ($albums as $album)
		{
			if ($this->user->is_user($album['album_user_id']))
			{
				$a_id = self::OWN_ALBUM;
			}
			else if ($album['album_user_id'] > self::PUBLIC_ALBUM)
			{
				$a_id = self::PERSONAL_ALBUM;
			}
			else
			{
				$a_id = $album['album_id'];
			}
			if ($this->_auth_data[$a_id]->get_bit($bit) && (!$display_in_rrc || ($display_in_rrc && $album['display_in_rrc'])) && ($display_pegas || ($album['album_user_id'] == self::PUBLIC_ALBUM)))
			{
				if ($return == 'bool')
				{
					return true;
				}
				$album_list .= (($album_list) ? ', ' : '') . $album['album_id'];
				$album_array[] = (int) $album['album_id'];
			}
		}

		if ($return == 'bool')
		{
			return false;
		}

		return ($return == 'array') ? $album_array : $album_list;
	}

	/**
	 * Get all user IDs that have specific ACL for album
	 *
	 * @param    string $acl      One of the permissions, Exp: i_view; *_count permissions are not allowed!
	 * @param    int    $album_id Album ID we want info for
	 *
	 * return    array    $user_ids    Return user IDs as array
	 * @return array
	 */
	public function acl_users_ids($acl, $album_id)
	{
		if (strstr($acl, '_count') != 0)
		{
			return array();
		}
		// Let's load album data
		$sql = 'SELECT * FROM ' . $this->table_albums . ' WHERE album_id = ' . (int) $album_id;
		$result = $this->db->sql_query($sql);
		$album_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// Let's request roles
		// If album user_id is different then 0 then this is user album.
		// So we need to request all roles for perm_system -2(own) and -3(user)
		if ($album_data['album_user_id'] != 0)
		{
			$sql = 'SELECT * FROM ' . $this->table_permissions . ' WHERE ' . $this->db->sql_in_set('perm_system', array(-2, -3));
		}
		else
		{
			$sql = 'SELECT * FROM ' . $this->table_permissions . ' WHERE perm_album_id = ' . $album_id;
		}

		$result = $this->db->sql_query($sql);
		$roles_id = array();
		// Now we build the array to test
		while ($row = $this->db->sql_fetchrow($result))
		{
			$roles_id['roles'][] = (int) $row['perm_role_id'];
			$roles_id[$row['perm_role_id']]['user_id'][] = (int) $row['perm_user_id'];
			$roles_id[$row['perm_role_id']]['group_id'][] = (int) $row['perm_group_id'];
		}
		$this->db->sql_freeresult($result);

		// Now we will select the roles that have the setted ACL
		$sql = 'SELECT role_id FROM ' . $this->table_roles . ' WHERE ' . $acl . ' = 1 and ' . $this->db->sql_in_set('role_id', $roles_id['roles'], false, true);
		$result = $this->db->sql_query($sql);
		$roles = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$roles[] = (int) $row['role_id'];
		}
		$this->db->sql_freeresult($result);

		// Let's cycle trough roles and build user_ids with user_ids from roles
		$user_ids = array();
		foreach ($roles as $id)
		{
			$user_ids = array_merge($user_ids, $roles_id[$id]['user_id']);
			// Let's query groups
			$sql = 'SELECT * FROM ' . USER_GROUP_TABLE . ' WHERE ' . $this->db->sql_in_set('group_id', $roles_id[$id]['group_id'], false, true);
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($row['user_pending'] == 0)
				{
					$user_ids[] = $row['user_id'];
				}
			}
			$this->db->sql_freeresult($result);
		}

		// Now we cycle the $user_ids to remove 0 and make ids unique
		$returning_value = array();
		foreach ($user_ids as $id)
		{
			if ($id != 0)
			{
				$returning_value[$id] = (int) $id;
			}
		}

		$user_ids = array();
		foreach ($returning_value as $id)
		{
			$user_ids[] = (int) $id;
		}
		return $user_ids;
	}

	/*
	* Get all albums that user has no access
	* return array	$exclude All albums we have no access due to zebra restrictions
	*/
	public function get_exclude_zebra()
	{
		$zebra_array = $this->get_user_zebra($this->phpbb_user->data['user_id']);
		$albums = $this->cache->get_albums();
		$exclude = array();
		foreach ($albums as $album)
		{
			// There is zebra only for users
			if ($album['album_type'] == 1 && $album['album_user_id'] > 0 && $this->get_zebra_state($zebra_array, $album['album_user_id'], $album['album_id']) < $album['album_auth_access'])
			{
				$exclude[] = (int) $album['album_id'];
			}
		}
		return $exclude;
	}
}
