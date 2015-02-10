<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/

namespace phpbbgallery\core;

class notification
{
	/**
	* Add images to watch-list
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete from the watch-list.
	* @param	int		$user_id		If not set, it uses the currents user_id
	*/
	static public function add($image_ids, $user_id = false)
	{
		global $db, $user, $table_prefix;

		$image_ids = self::cast_mixed_int2array($image_ids);
		$user_id = (int) (($user_id) ? $user_id : $user->data['user_id']);

		// First check if we are not subscribed alredy for some
		$sql = 'SELECT * FROM ' . $table_prefix . 'gallery_watch  WHERE user_id = ' . $user_id . ' and ' . $db->sql_in_set('image_id', $image_ids);
		$result = $db->sql_query($sql);
		$exclude = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$exclude[] = (int) $row['image_id'];
		}
		$image_ids = array_diff($image_ids, $exclude);

		foreach ($image_ids as $image_id)
		{
			$sql_ary = array(
				'image_id'		=> $image_id,
				'user_id'		=> $user_id,
			);
			$sql = 'INSERT INTO ' . $table_prefix . 'gallery_watch ' . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);
		}
	}
	/**
	* Add albums to watch-list
	*
	* @param	mixed	$album_ids		Array or integer with album_id where we delete from the watch-list.
	* @param	int		$user_id		If not set, it uses the currents user_id
	*/
	static public function add_albums($album_ids, $user_id = false)
	{
		global $db, $user, $table_prefix;

		$album_ids = self::cast_mixed_int2array($album_ids);
		$user_id = (int) (($user_id) ? $user_id : $user->data['user_id']);

		foreach ($album_ids as $album_id)
		{
			$sql_ary = array(
				'album_id'		=> $album_id,
				'user_id'		=> $user_id,
			);
			$sql = 'INSERT INTO ' . $table_prefix . 'gallery_watch ' . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);
		}
	}

	/**
	* Remove images from watch-list
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete from the watch-list.
	* @param	mixed	$user_ids		If not set, it uses the currents user_id
	*/
	static public function remove($image_ids, $user_ids = false)
	{
		global $db, $user, $table_prefix;

		$image_ids = self::cast_mixed_int2array($image_ids);
		$user_ids = self::cast_mixed_int2array((($user_ids) ? $user_ids : $user->data['user_id']));

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_watch 
			WHERE ' . $db->sql_in_set('user_id', $user_ids) . '
				AND ' . $db->sql_in_set('image_id', $image_ids);
		$db->sql_query($sql);
	}

	/**
	* Remove albums from watch-list
	*
	* @param	mixed	$album_ids		Array or integer with album_id where we delete from the watch-list.
	* @param	mixed	$user_ids		If not set, it uses the currents user_id
	*/
	static public function remove_albums($album_ids, $user_ids = false)
	{
		global $db, $user;

		$album_ids = self::cast_mixed_int2array($album_ids);
		$user_ids = self::cast_mixed_int2array((($user_ids) ? $user_ids : $user->data['user_id']));

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_watch 
			WHERE ' . $db->sql_in_set('user_id', $user_ids) . '
				AND ' . $db->sql_in_set('album_id', $album_ids);
		$db->sql_query($sql);
	}

	/**
	* Delete given image_ids from watch-list
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete from watch-list.
	*/
	static public function delete_images($image_ids)
	{
		global $db, $table_prefix;

		$image_ids = self::cast_mixed_int2array($image_ids);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_watch 
			WHERE ' . $db->sql_in_set('image_id', $image_ids);
		$result = $db->sql_query($sql);
	}


	/**
	* Delete given album_ids from watch-list
	*
	* @param	mixed	$album_ids		Array or integer with album_id where we delete from watch-list.
	*/
	static public function delete_albums($album_ids)
	{
		global $db, $table_prefix;

		$album_ids = self::cast_mixed_int2array($album_ids);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_watch 
			WHERE ' . $db->sql_in_set('album_id', $album_ids);
		$result = $db->sql_query($sql);
	}

	/**
	* Gallery Notification
	*
	* borrowed from phpBB3
	* @author: phpBB Group
	* @function: user_notification
	*/
	static public function send_notification($mode, $handle_id, $image_name)
	{
		global $user, $db, $album_id, $image_id, $image_data, $album_data, $table_prefix;

		$help_mode = $mode . '_id';
		$mode_id = $$help_mode;
		$mode_notification = ($mode == 'album') ? 'image' : 'comment';

		// Get banned User ID's
		$sql = 'SELECT ban_userid
			FROM ' . BANLIST_TABLE . '
			WHERE ban_userid <> 0
				AND ban_exclude <> 1';
		$result = $db->sql_query($sql);

		$sql_ignore_users = ANONYMOUS . ', ' . $user->data['user_id'];
		while ($row = $db->sql_fetchrow($result))
		{
			$sql_ignore_users .= ', ' . (int) $row['ban_userid'];
		}
		$db->sql_freeresult($result);

		$notify_rows = array();

		// -- get album_userids	|| image_userids
		$sql = 'SELECT u.user_id, u.username, u.user_email, u.user_lang, u.user_notify_type, u.user_jabber
			FROM ' . $table_prefix . 'gallery_watch w, ' . USERS_TABLE . ' u
			WHERE w.' . $help_mode . ' = ' . $handle_id . "
				AND w.user_id NOT IN ($sql_ignore_users)
				AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')
				AND u.user_id = w.user_id';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$notify_rows[$row['user_id']] = array(
				'user_id'		=> $row['user_id'],
				'username'		=> $row['username'],
				'user_email'	=> $row['user_email'],
				'user_jabber'	=> $row['user_jabber'],
				'user_lang'		=> $row['user_lang'],
				'notify_type'	=> ($mode != 'album') ? 'image' : 'album',
				'template'		=> "new{$mode_notification}_notify",
				'method'		=> $row['user_notify_type'],
				'allowed'		=> false
			);
		}
		$db->sql_freeresult($result);

		if (!sizeof($notify_rows))
		{
			return;
		}

		// Get album_user_id to check for personal albums.
		$sql = 'SELECT album_id, album_user_id
			FROM ' . $table_prefix . 'gallery_albums
			WHERE album_id = ' . $handle_id;
		$result = $db->sql_query($sql);
		$album = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		if (empty($album))
		{
			trigger_error('ALBUM_NOT_EXIST');
		}

		// Make sure users are allowed to view the album
		$i_view_ary = $groups_ary = $groups_row = array();
		$sql_array = array(
			'SELECT'		=> 'pr.i_view, p.perm_system, p.perm_group_id, p.perm_user_id',
			'FROM'			=> array($table_prefix . 'gallery_permissions' => 'p'),

			'LEFT_JOIN'		=> array(
				array(
					'FROM'		=> array($table_prefix . 'gallery_roles' => 'pr'),
					'ON'		=> 'p.perm_role_id = pr.role_id',
				),
			),

			'WHERE'			=> (($album['album_user_id'] == \phpbbgallery\core\album\album::PUBLIC_ALBUM) ? 'p.perm_album_id = ' . $album_id : 'p.perm_system <> ' . \phpbbgallery\core\album\album::PUBLIC_ALBUM),
			'ORDER_BY'		=> 'pr.i_view ASC',
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['perm_group_id'])
			{
				$groups_ary[] = $row['perm_group_id'];
				$groups_row[$row['perm_group_id']] = $row;
			}
			else
			{
				if (!isset($i_view_ary[$row['perm_user_id']]) || (isset($i_view_ary[$row['perm_user_id']]) && ($i_view_ary[$row['perm_user_id']] < $row['i_view'])))
				{
					if (!$row['perm_system'])
					{
						$i_view_ary[$row['perm_user_id']] = $row['i_view'];
					}
					else if (($row['perm_system'] == \phpbbgallery\core\auth\auth::OWN_ALBUM) && ($album['album_user_id'] == $row['perm_user_id']))
					{
						$i_view_ary[$row['perm_user_id']] = $row['i_view'];
					}
					else if (($row['perm_system'] ==\phpbbgallery\core\auth\auth::PERSONAL_ALBUM) && ($album['album_user_id'] != $row['perm_user_id']))
					{
						$i_view_ary[$row['perm_user_id']] = $row['i_view'];
					}
				}
			}
		}
		$db->sql_freeresult($result);

		if (sizeof($groups_ary))
		{
			// Get all users by their group permissions
			$sql = 'SELECT user_id, group_id
				FROM ' . USER_GROUP_TABLE . '
				WHERE ' . $db->sql_in_set('group_id', $groups_ary) . '
					AND user_pending = 0';
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				if (!isset($i_view_ary[$row['user_id']]) || (isset($i_view_ary[$row['user_id']]) && ($i_view_ary[$row['user_id']] < $groups_row[$row['group_id']]['i_view'])))
				{
					if (!$groups_row[$row['group_id']]['perm_system'])
					{
						$i_view_ary[$row['user_id']] = $groups_row[$row['group_id']]['i_view'];
					}
					else if (($groups_row[$row['group_id']]['perm_system'] == \phpbbgallery\core\auth\auth::OWN_ALBUM) && ($album['album_user_id'] == $row['user_id']))
					{
						$i_view_ary[$row['user_id']] = $groups_row[$row['group_id']]['i_view'];
					}
					else if (($groups_row[$row['group_id']]['perm_system'] == \phpbbgallery\core\auth\auth::PERSONAL_ALBUM) && ($album['album_user_id'] != $row['user_id']))
					{
						$i_view_ary[$row['user_id']] = $groups_row[$row['group_id']]['i_view'];
					}
				}
			}
			$db->sql_freeresult($result);
		}

		// Now, we have to do a little step before really sending, we need to distinguish our users a little bit. ;)
		$msg_users = $delete_ids = $update_notification = array();
		foreach ($notify_rows as $user_id => $row)
		{
			if (($i_view_ary[$row['user_id']] != \phpbbgallery\core\auth\auth::ACL_YES) || !trim($row['user_email']))
			{
				$delete_ids[$row['notify_type']][] = $row['user_id'];
			}
			else
			{
				$msg_users[] = $row;
				$update_notification[$row['notify_type']][] = $row['user_id'];
			}
		}
		unset($notify_rows);

		// Now, we are able to really send out notifications
		if (sizeof($msg_users))
		{
			if (!class_exists('messenger'))
			{
				\phpbbgallery\core\url::_include('functions_messenger', 'phpbb');
			}
			$messenger = new messenger();

			$msg_list_ary = array();
			foreach ($msg_users as $row)
			{
				$pos = (!isset($msg_list_ary[$row['template']])) ? 0 : sizeof($msg_list_ary[$row['template']]);

				$msg_list_ary[$row['template']][$pos]['method']	= $row['method'];
				$msg_list_ary[$row['template']][$pos]['email']	= $row['user_email'];
				$msg_list_ary[$row['template']][$pos]['jabber']	= $row['user_jabber'];
				$msg_list_ary[$row['template']][$pos]['name']	= $row['username'];
				$msg_list_ary[$row['template']][$pos]['lang']	= $row['user_lang'];
			}
			unset($msg_users);

			foreach ($msg_list_ary as $email_template => $email_list)
			{
				foreach ($email_list as $addr)
				{
					$messenger->template($email_template, $addr['lang']);

					$messenger->to($addr['email'], $addr['name']);
					$messenger->im($addr['jabber'], $addr['name']);

					$messenger->assign_vars(array(
						'USERNAME'		=> htmlspecialchars_decode($addr['name']),
						'IMAGE_NAME'	=> htmlspecialchars_decode($image_name),
						'ALBUM_NAME'	=> htmlspecialchars_decode($album_data['album_name']),

						'U_ALBUM'				=> \phpbbgallery\core\url::create_link('full', 'album', "album_id=$album_id"),
						'U_IMAGE'				=> \phpbbgallery\core\url::create_link('full', 'image_page', "album_id=$album_id&amp;image_id=$image_id"),
						'U_NEWEST_POST'			=> \phpbbgallery\core\url::create_link('full', 'viewtopic', "album_id=$album_id&amp;image_id=$image_id"),
						'U_STOP_WATCHING_IMAGE'	=> \phpbbgallery\core\url::create_link('full', 'image_page', "mode=unwatch&amp;album_id=$album_id&amp;image_id=$image_id"),
						'U_STOP_WATCHING_ALBUM'	=> \phpbbgallery\core\url::create_link('full', 'album', "mode=unwatch&amp;album_id=$album_id"),
					));

					$messenger->send($addr['method']);
				}
			}
			unset($msg_list_ary);

			$messenger->save_queue();
		}

		// Now delete the user_ids not authorised to receive notifications on this image/album
		if (!empty($delete_ids['image']))
		{
			self::remove($image_id, $delete_ids['image']);
		}

		if (!empty($delete_ids['album']))
		{
			self::remove_albums($album_id, $delete_ids['album']);
		}
	}

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
