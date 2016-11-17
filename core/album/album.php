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
	protected $albums_table;
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user,
	\phpbbgallery\core\auth\auth $gallery_auth, \phpbbgallery\core\cache $gallery_cache, \phpbbgallery\core\block $block,
	\phpbbgallery\core\config $gallery_config,
	$albums_table, $images_table, $watch_table, $contest_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->gallery_auth = $gallery_auth;
		$this->gallery_cache = $gallery_cache;
		$this->block = $block;
		$this->gallery_config = $gallery_config;
		$this->albums_table = $albums_table;
		$this->images_table = $images_table;
		$this->watch_table = $watch_table;
		$this->contests_table = $contest_table;
	}

	/**
	 * Get album information
	 *
	 * @param      $album_id
	 * @param bool $extended_info
	 * @return mixed
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
	 *
	 * @param      $album_id
	 * @param bool $user_id
	 * @return bool
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
	 * @param    bool $ignore_personals list personal albums
	 * @param    string $select_name request_var() for the select-box
	 * @param bool|int $select_id selected album
	 * @param bool|string $requested_permission Exp: for moving a image you need i_upload permissions or a_moderate
	 * @param bool $ignore_id
	 * @param int $album_user_id for the select-boxes of the ucp so you only can attach to your own albums
	 * @param    int $requested_album_type only albums of the album_type are allowed
	 * @return string $gallery_albumbox        if ($select_name) {full select-box} else {list with options}
	 * else {list with options}
	 *
	 * comparable to make_forum_select (includes/functions_admin.php)
	 * @internal param $ (string || array)    $ignore_id                disabled albums, Exp: on moving: the album where the image is now
	 */
	public function get_albumbox($ignore_personals, $select_name, $select_id = false, $requested_permission = false, $ignore_id = false, $album_user_id = \phpbbgallery\core\block::PUBLIC_ALBUM, $requested_album_type = -1)
	{
		// Instead of the query we use the cache
		$album_data = $this->gallery_cache->get('albums');

		$right = $last_a_u_id = 0;
		$access_own = $access_personal = $requested_own = $requested_personal = false;
		$c_access_own = $c_access_personal = false;
		$padding_store = array('0' => '');
		$padding = $album_list = '';
		$check_album_type = ($requested_album_type >= 0) ? true : false;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);

		// Sometimes it could happen that albums will be displayed here not be displayed within the index page
		// This is the result of albums not displayed at index and a parent of a album with no permissions.
		// If this happens, the padding could be "broken", see includes/functions_admin.php > make_forum_select

		foreach ($album_data as $row)
		{
			$list = false;
			if ($row['album_user_id'] != $last_a_u_id)
			{
				if (!$last_a_u_id && $this->gallery_auth->acl_check('a_list', $this->gallery_auth->get_personal_album()) && !$ignore_personals)
				{
					$album_list .= '<option disabled="disabled" class="disabled-option">' . $this->user->lang['PERSONAL_ALBUMS'] . '</option>';
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
			(($requested_permission == 'm_move') && (($row['album_type'] == \phpbbgallery\core\block::TYPE_CAT) || (!$this->gallery_auth->acl_check('i_upload', $row['album_id'], $row['album_user_id']) && !$this->gallery_auth->acl_check('m_move', $row['album_id'], $row['album_user_id']))))
			||
			// album_type does not fit
			($check_album_type && ($row['album_type'] != $requested_album_type))
			)
			{
				$disabled = true;
			}

			if (($select_id == $this->gallery_auth->get_setting_permissions()) && !$row['album_user_id'])
			{
				$list = true;
			}
			else if (!$row['album_user_id'])
			{
				if ($this->gallery_auth->acl_check('a_list', $row['album_id'], $row['album_user_id']) || defined('IN_ADMIN'))
				{
					$list = true;
				}
			}
			else if (!$ignore_personals)
			{
				if ($row['album_user_id'] == $this->user->data['user_id'])
				{
					if (!$c_access_own)
					{
						$c_access_own = true;
						$access_own = $this->gallery_auth->acl_check('a_list', $this->gallery_auth->get_own_album());
						if ($requested_permission)
						{
							$requested_own = !$this->gallery_auth->acl_check($requested_permission, $this->gallery_auth->get_own_album());
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
						$access_personal = $this->gallery_auth->acl_check('a_list', $this->gallery_auth->get_personal_album());
						if ($requested_permission)
						{
							$requested_personal = !$this->gallery_auth->acl_check($requested_permission, $this->gallery_auth->get_personal_album());
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
			if (($album_user_id != \phpbbgallery\core\block::PUBLIC_ALBUM) && ($album_user_id != $row['album_user_id']))
			{
				$list = false;
			}
			else if (($album_user_id != \phpbbgallery\core\block::PUBLIC_ALBUM) && ($row['parent_id'] == 0))
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
	 * @param $album_id
	 * @return mixed
	 */
	public function update_info($album_id)
	{
		$images_real = $images = $album_user_id = 0;

		// Get the album_user_id, so we can keep the user_colour
		$sql = 'SELECT album_user_id
			FROM ' . $this->albums_table .'
			WHERE album_id = ' . (int) $album_id;
		$result = $this->db->sql_query($sql);
		$album_user_id = $this->db->sql_fetchfield('album_user_id');
		$this->db->sql_freeresult($result);

		// Number of not unapproved images
		$sql = 'SELECT COUNT(image_id) images
			FROM ' . $this->images_table .' 
			WHERE image_status <> ' . $this->block->get_image_status_unapproved() . '
				AND image_status <> ' . $this->block->get_image_status_orphan() . '
				AND image_album_id = ' . (int) $album_id;
		$result = $this->db->sql_query($sql);
		$images = $this->db->sql_fetchfield('images');
		$this->db->sql_freeresult($result);

		// Number of total images
		$sql = 'SELECT COUNT(image_id) images_real
			FROM ' . $this->images_table .'
			WHERE image_status <> ' . $this->block->get_image_status_orphan() . '
				AND image_album_id = ' . (int) $album_id;
		$result = $this->db->sql_query($sql);
		$images_real = $this->db->sql_fetchfield('images_real');
		$this->db->sql_freeresult($result);

		// Data of the last not unapproved image
		$sql = 'SELECT image_id, image_time, image_name, image_username, image_user_colour, image_user_id
			FROM ' . $this->images_table .'
			WHERE image_status <> ' . $this->block->get_image_status_unapproved() . '
				AND image_status <> ' . $this->block->get_image_status_orphan() . '
				AND image_album_id = ' . (int) $album_id . '
			ORDER BY image_time DESC';
		$result = $this->db->sql_query($sql);
		if ($row = $this->db->sql_fetchrow($result))
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
		$this->db->sql_freeresult($result);

		$sql = 'UPDATE ' . $this->albums_table .' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE album_id = ' . (int) $album_id;
		$this->db->sql_query($sql);

		return $row;
	}

	/**
	 * Generate personal album for user, when moving image into it
	 * @param $album_name
	 * @param $user_id
	 * @param $user_colour
	 * @param $gallery_user
	 * @return string
	 */
	public function generate_personal_album($album_name, $user_id, $user_colour, $gallery_user)
	{
		$album_data = array(
			'album_name'					=> $album_name,
			'parent_id'						=> 0,
			//left_id and right_id default by db
			'album_desc_options'			=> 7,
			'album_desc'					=> '',
			'album_parents'					=> '',
			'album_type'					=> \phpbbgallery\core\block::TYPE_UPLOAD,
			'album_status'					=> \phpbbgallery\core\block::ALBUM_OPEN,
			'album_user_id'					=> $user_id,
			'album_last_username'			=> '',
			'album_last_user_colour'		=> $user_colour,
		);
		$this->db->sql_query('INSERT INTO ' . $this->albums_table. ' ' . $this->db->sql_build_array('INSERT', $album_data));
		$personal_album_id = $this->db->sql_nextid();

		$gallery_user->update_data(array(
				'personal_album_id'	=> $personal_album_id,
		));

		$this->gallery_config->inc('num_pegas', 1);

		// Update the config for the statistic on the index
		$this->gallery_config->set('newest_pega_user_id', $user_id);
		$this->gallery_config->set('newest_pega_username', $album_name);
		$this->gallery_config->set('newest_pega_user_colour', $user_colour);
		$this->gallery_config->set('newest_pega_album_id', $personal_album_id);

		$this->gallery_cache->destroy('_albums');
		$this->gallery_cache->destroy('sql', $this->albums_table);

		return $personal_album_id;
	}

	/**
	* Create array of album IDs that are public
	*/
	public function get_public_albums()
	{
		$sql = 'SELECT album_id
				FROM ' . $this->albums_table . '
				WHERE album_user_id = ' . \phpbbgallery\core\block::PUBLIC_ALBUM;
		$result = $this->db->sql_query($sql);
		$id_ary = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$id_ary[] = (int) $row['album_id'];
		}
		$this->db->sql_freeresult($result);
		return $id_ary;
	}
}
