<?php

/**
*
* @package NV Newspage Extension
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\album;

class album
{
	const PUBLIC_ALBUM		= 0;

	const TYPE_CAT			= 0;
	const TYPE_UPLOAD		= 1;
	const TYPE_CONTEST		= 2;

	const STATUS_OPEN		= 0;
	const STATUS_LOCKED		= 1;

	protected $albums_table;
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user,
	$albums_table, $watch_table, $contest_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->albums_table = $albums_table;
		$this->watch_table = $watch_table;
		$this->contests_table = $contest_table;
	}
	/**
	* Get locked
	*/
	public function get_status_locked()
	{
		return 1;
	}

	static public function get_public()
	{
		return 0;
	}

	static public function get_type_upload()
	{
		return 1;
	}
	/**
	* Get album information
	*/
	public function get_info($album_id, $extended_info = true)
	{
		$sql_array = array(
			'SELECT'		=> 'a.*',
			'FROM'			=> array($this->albums_table => 'a'),

			'WHERE'			=> 'a.album_id = ' . (int) $album_id,
		);

		if ($extended_info)
		{
			$sql_array['SELECT'] .= ', c.*, w.watch_id';
			$sql_array['LEFT_JOIN'] = array(
				array(
					'FROM'		=> array($this->watch_table => 'w'),
					'ON'		=> 'a.album_id = w.album_id AND w.user_id = ' . (int) $this->user->data['user_id'],
				),
				array(
					'FROM'		=> array($this->contests_table => 'c'),
					'ON'		=> 'a.album_id = c.contest_album_id',
				),
			);
		}
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			throw new \phpbb\exception\http_exception(404, 'ALBUM_NOT_EXIST');
		}

		if ($extended_info  && !isset($row['contest_id']))
		{
			$row['contest_id'] = 0;
			$row['contest_rates_start'] = 0;
			$row['contest_end'] = 0;
			$row['contest_marked'] = 0;
			$row['contest_first'] = 0;
			$row['contest_second'] = 0;
			$row['contest_third'] = 0;
		}

		return $row;
	}

	/**
	* Check whether the album_user is the user who wants to do something
	*/
	public function check_user($album_id, $user_id = false)
	{
		if ($user_id === false)
		{
			$user_id = (int) $this->user->data['user_id'];
		}
		else
		{
			$user_id = (int) $user_id;
		}

		$sql = 'SELECT album_id
			FROM ' . $this->albums_table . '
			WHERE album_id = ' . (int) $album_id . '
				AND album_user_id = ' . $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row === false)
		{
			// return false;
			throw new \phpbb\exception\http_exception(403, 'NO_ALBUM_STEALING');
		}

		return true;
	}

	/**
	* Generate gallery-albumbox
	* @param	bool				$ignore_personals		list personal albums
	* @param	string				$select_name			request_var() for the select-box
	* @param	int					$select_id				selected album
	* @param	string				$requested_permission	Exp: for moving a image you need i_upload permissions or a_moderate
	* @param	(string || array)	$ignore_id				disabled albums, Exp: on moving: the album where the image is now
	* @param	int					$album_user_id			for the select-boxes of the ucp so you only can attach to your own albums
	* @param	int					$requested_album_type	only albums of the album_type are allowed
	*
	* @return	string				$gallery_albumbox		if ($select_name) {full select-box} else {list with options}
	*
	* comparable to make_forum_select (includes/functions_admin.php)
	*/
	function get_albumbox($ignore_personals, $select_name, $select_id = false, $requested_permission = false, $ignore_id = false, $album_user_id = self::PUBLIC_ALBUM, $requested_album_type = -1)
	{
		global $db, $user, $phpbb_ext_gallery, $cache, $phpbb_dispatcher, $table_name, $permissions_table, $roles_table, $users_table;
		global $phpbb_container;

		// Inint auth
		$gallery_cache = new \phpbbgallery\core\cache($cache, $db);
		$gallery_user = $phpbb_container->get('phpbbgallery.core.user');
		$phpbb_ext_gallery_core_auth = $phpbb_container->get('phpbbgallery.core.auth');

		// Instead of the query we use the cache
		$album_data = $gallery_cache->get('albums');

		$right = $last_a_u_id = 0;
		$access_own = $access_personal = $requested_own = $requested_personal = false;
		$c_access_own = $c_access_personal = false;
		$padding_store = array('0' => '');
		$padding = $album_list = '';
		$check_album_type = ($requested_album_type >= 0) ? true : false;
		$phpbb_ext_gallery_core_auth->load_user_premissions($user->data['user_id']);

		// Sometimes it could happen that albums will be displayed here not be displayed within the index page
		// This is the result of albums not displayed at index and a parent of a album with no permissions.
		// If this happens, the padding could be "broken", see includes/functions_admin.php > make_forum_select

		foreach ($album_data as $row)
		{
			$list = false;
			if ($row['album_user_id'] != $last_a_u_id)
			{
				if (!$last_a_u_id && $phpbb_ext_gallery_core_auth->acl_check('a_list', $phpbb_ext_gallery_core_auth::PERSONAL_ALBUM) && !$ignore_personals)
				{
					$album_list .= '<option disabled="disabled" class="disabled-option">' . $user->lang['PERSONAL_ALBUMS'] . '</option>';
				}
				$padding = '';
				$padding_store[$row['parent_id']] = '';
			}
			if ($row['left_id'] < $right)
			{
				$padding .= '&nbsp; &nbsp;';
				$padding_store[$row['parent_id']] = $padding;
			}
			else if ($row['left_id'] > $right + 1)
			{
				$padding = (isset($padding_store[$row['parent_id']])) ? $padding_store[$row['parent_id']] : '';
			}

			$right = $row['right_id'];
			$last_a_u_id = $row['album_user_id'];
			$disabled = false;

			if (
			// Is in the ignore_id
			((is_array($ignore_id) && in_array($row['album_id'], $ignore_id)) || $row['album_id'] == $ignore_id)
			||
			// Need upload permissions (for moving)
			(($requested_permission == 'm_move') && (($row['album_type'] == self::TYPE_CAT) || (!$phpbb_ext_gallery_core_auth->acl_check('i_upload', $row['album_id'], $row['album_user_id']) && !$phpbb_ext_gallery_core_auth->acl_check('m_move', $row['album_id'], $row['album_user_id']))))
			||
			// album_type does not fit
			($check_album_type && ($row['album_type'] != $requested_album_type))
			)
			{
				$disabled = true;
			}

			if (($select_id == $phpbb_ext_gallery_core_auth::SETTING_PERMISSIONS) && !$row['album_user_id'])
			{
				$list = true;
			}
			else if (!$row['album_user_id'])
			{
				if ($phpbb_ext_gallery_core_auth->acl_check('a_list', $row['album_id'], $row['album_user_id']) || defined('IN_ADMIN'))
				{
					$list = true;
				}
			}
			else if (!$ignore_personals)
			{
				if ($row['album_user_id'] == $user->data['user_id'])
				{
					if (!$c_access_own)
					{
						$c_access_own = true;
						$access_own = $phpbb_ext_gallery_core_auth->acl_check('a_list', $phpbb_ext_gallery_core_auth::OWN_ALBUM);
						if ($requested_permission)
						{
							$requested_own = !$phpbb_ext_gallery_core_auth->acl_check($requested_permission, $phpbb_ext_gallery_core_auth::OWN_ALBUM);
						}
						else
						{
							$requested_own = false; // We need the negated version of true here
						}
					}
					$list = (!$list) ? $access_own : $list;
					$disabled = (!$disabled) ? $requested_own : $disabled;
				}
				else if ($row['album_user_id'])
				{
					if (!$c_access_personal)
					{
						$c_access_personal = true;
						$access_personal = $phpbb_ext_gallery_core_auth->acl_check('a_list', $phpbb_ext_gallery_core_auth::PERSONAL_ALBUM);
						if ($requested_permission)
						{
							$requested_personal = !$phpbb_ext_gallery_core_auth->acl_check($requested_permission, $phpbb_ext_gallery_core_auth::PERSONAL_ALBUM);
						}
						else
						{
							$requested_personal = false; // We need the negated version of true here
						}
					}
					$list = (!$list) ? $access_personal : $list;
					$disabled = (!$disabled) ? $requested_personal : $disabled;
				}
			}
			if (($album_user_id != self::PUBLIC_ALBUM) && ($album_user_id != $row['album_user_id']))
			{
				$list = false;
			}
			else if (($album_user_id != self::PUBLIC_ALBUM) && ($row['parent_id'] == 0))
			{
				$disabled = true;
			}

			if ($list)
			{
				$selected = (is_array($select_id)) ? ((in_array($row['album_id'], $select_id)) ? ' selected="selected"' : '') : (($row['album_id'] == $select_id) ? ' selected="selected"' : '');
				$album_list .= '<option value="' . $row['album_id'] . '"' . (($disabled) ? ' disabled="disabled" class="disabled-option"' : $selected) . '>' . $padding . $row['album_name'] . ' (ID: ' . $row['album_id'] . ')</option>';
			}
		}
		unset($padding_store);

		if ($select_name)
		{
			$gallery_albumbox = "<select name='$select_name' id='$select_name'>";
			$gallery_albumbox .= $album_list;
			$gallery_albumbox .= '</select>';
		}
		else
		{
			$gallery_albumbox = $album_list;
		}

		return $gallery_albumbox;
	}

	/**
	* Update album information
	* Resets the following columns with the correct value:
	* - album_images, _real
	* - album_last_image_id, _time, _name
	* - album_last_username, _user_colour, _user_id
	*/
	static public function update_info($album_id)
	{
		global $db, $table_prefix, $phpbb_container;

		// Define some classes
		$phpbb_ext_gallery_core_image = $phpbb_container->get('phpbbgallery.core.image');

		$images_real = $images = $album_user_id = 0;

		// Get the album_user_id, so we can keep the user_colour
		$sql = 'SELECT album_user_id
			FROM ' . $table_prefix .'gallery_albums
			WHERE album_id = ' . (int) $album_id;
		$result = $db->sql_query($sql);
		$album_user_id = $db->sql_fetchfield('album_user_id');
		$db->sql_freeresult($result);

		// Number of not unapproved images
		$sql = 'SELECT COUNT(image_id) images
			FROM ' . $table_prefix .'gallery_images
			WHERE image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . '
				AND image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
				AND image_album_id = ' . (int) $album_id;
		$result = $db->sql_query($sql);
		$images = $db->sql_fetchfield('images');
		$db->sql_freeresult($result);

		// Number of total images
		$sql = 'SELECT COUNT(image_id) images_real
			FROM ' . $table_prefix .'gallery_images
			WHERE image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
				AND image_album_id = ' . (int) $album_id;
		$result = $db->sql_query($sql);
		$images_real = $db->sql_fetchfield('images_real');
		$db->sql_freeresult($result);

		// Data of the last not unapproved image
		$sql = 'SELECT image_id, image_time, image_name, image_username, image_user_colour, image_user_id
			FROM ' . $table_prefix .'gallery_images
			WHERE image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . '
				AND image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
				AND image_album_id = ' . (int) $album_id . '
			ORDER BY image_time DESC';
		$result = $db->sql_query($sql);
		if ($row = $db->sql_fetchrow($result))
		{
			$sql_ary = array(
				'album_images_real'			=> $images_real,
				'album_images'				=> $images,
				'album_last_image_id'		=> $row['image_id'],
				'album_last_image_time'		=> $row['image_time'],
				'album_last_image_name'		=> $row['image_name'],
				'album_last_username'		=> $row['image_username'],
				'album_last_user_colour'	=> $row['image_user_colour'],
				'album_last_user_id'		=> $row['image_user_id'],
			);
		}
		else
		{
			// No approved image, so we clear the columns
			$sql_ary = array(
				'album_images_real'			=> $images_real,
				'album_images'				=> $images,
				'album_last_image_id'		=> 0,
				'album_last_image_time'		=> 0,
				'album_last_image_name'		=> '',
				'album_last_username'		=> '',
				'album_last_user_colour'	=> '',
				'album_last_user_id'		=> 0,
			);
			if ($album_user_id)
			{
				unset($sql_ary['album_last_user_colour']);
			}
		}
		$db->sql_freeresult($result);

		$sql = 'UPDATE ' . $table_prefix .'gallery_albums SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE ' . $db->sql_in_set('album_id', $album_id);
		$db->sql_query($sql);

		return $row;
	}

	/**
	* Generate personal album for user, when moving image into it
	*/
	public static function generate_personal_album($album_name, $user_id, $user_colour, $gallery_user)
	{
		global $cache, $db, $table_prefix, $config;

		$phpbb_gallery_config = new \phpbbgallery\core\config($config);
		$albums_table = $table_prefix . 'gallery_albums';

		$album_data = array(
			'album_name'					=> $album_name,
			'parent_id'						=> 0,
			//left_id and right_id default by db
			'album_desc_options'			=> 7,
			'album_desc'					=> '',
			'album_parents'					=> '',
			'album_type'					=> self::TYPE_UPLOAD,
			'album_status'					=> self::STATUS_OPEN,
			'album_user_id'					=> $user_id,
			'album_last_username'			=> '',
			'album_last_user_colour'		=> $user_colour,
		);
		$db->sql_query('INSERT INTO ' . $albums_table . ' ' . $db->sql_build_array('INSERT', $album_data));
		$personal_album_id = $db->sql_nextid();

		$gallery_user->update_data(array(
				'personal_album_id'	=> $personal_album_id,
		));

		$phpbb_gallery_config->inc('num_pegas', 1);

		// Update the config for the statistic on the index
		$phpbb_gallery_config->set('newest_pega_user_id', $user_id);
		$phpbb_gallery_config->set('newest_pega_username', $album_name);
		$phpbb_gallery_config->set('newest_pega_user_colour', $user_colour);
		$phpbb_gallery_config->set('newest_pega_album_id', $personal_album_id);

		$cache->destroy('_albums');
		$cache->destroy('sql', $albums_table);

		return $personal_album_id;
	}

	/**
	* Create array of album IDs that are public
	*/
	public function get_public_albums()
	{
		global $db, $table_prefix;
		$sql = 'SELECT album_id
				FROM ' . $table_prefix . 'gallery_albums
				WHERE album_user_id = ' . self::PUBLIC_ALBUM;
		$result = $db->sql_query($sql);
		$id_ary = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$id_ary[] = (int) $row['album_id'];
		}

		return $id_ary;
	}
}
