<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* mostly borrowed from phpBB3
* @author: phpBB Group
* @location: includes/acp/acp_forums.php
*
* Note: There are several code parts commented out, for example the album/forum_password.
*       I didn't remove them, to have it easier when I implement this feature one day. I hope it's okay.
*/

/**
* @ignore
*/

namespace phpbbgallery\core\album;

class manage
{
	public $user_id = 0;

	public $parent_id = 0;

	private $u_action = '';

	public function phpbb_ext_gallery_core_album_manage($user_id, $parent_id, $u_action = '')
	{
		$this->user_id = (int) $user_id;
		$this->parent_id = (int) $parent_id;
		$this->u_action = $u_action;
	}

	public function set_user($user_id)
	{
		$this->user_id = (int) $user_id;
	}

	public function set_parent($parent_id)
	{
		$this->parent_id = (int) $parent_id;
	}

	/**
	* Generate back link for acp pages
	*/
	static public function back_link($u_action)
	{
		global $user;
		return '<br /><br /><a href="' . $u_action . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
	}

	/**
	* Update album data
	*
	* borrowed from phpBB3
	* @author: phpBB Group
	* @function: update_forum_data
	*/
	public function update_album_data(&$album_data, &$contest_data)
	{
		global $db, $user, $cache, $table_prefix, $phpbb_container, $request;

		// define support class
		$phpbb_ext_gallery_core_album = $phpbb_container->get('phpbbgallery.core.album');
		$phpbb_ext_gallery_core_image = $phpbb_container->get('phpbbgallery.core.image');

		$errors = array();

		if (!$album_data['album_name'])
		{
			$errors[] = $user->lang['ALBUM_NAME_EMPTY'];
		}

		if (utf8_strlen($album_data['album_desc']) > 4000)
		{
			$errors[] = $user->lang['ALBUM_DESC_TOO_LONG'];
		}

		/*if ($album_data['album_password'] || $album_data['album_password_confirm'])
		{
			if ($album_data['album_password'] != $album_data['album_password_confirm'])
			{
				$album_data['album_password'] = $album_data['album_password_confirm'] = '';
				$errors[] = $user->lang['ALBUM_PASSWORD_MISMATCH'];
			}
		}*/
		// Validate the contest timestamps:
		if ($album_data['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CONTEST)
		{
			$start_date_error = $date_error = false;
			if (!preg_match('#(\\d{4})-(\\d{1,2})-(\\d{1,2}) (\\d{1,2}):(\\d{2})#', $contest_data['contest_start'], $m))
			{
				$errors[] = sprintf($user->lang['CONTEST_START_INVALID'], $contest_data['contest_start']);
				$start_date_error = true;
			}
			else
			{
				$contest_data['contest_start'] = gmmktime($m[4], $m[5], 0, $m[2], $m[3], $m[1]) - ($user->data['user_timezone']);
			}
			if (!preg_match('#(\\d{4})-(\\d{1,2})-(\\d{1,2}) (\\d{1,2}):(\\d{2})#', $contest_data['contest_rating'], $m))
			{
				$errors[] = sprintf($user->lang['CONTEST_RATING_INVALID'], $contest_data['contest_rating']);
				$date_error = true;
			}
			else if (!$start_date_error)
			{
				$contest_data['contest_rating'] = gmmktime($m[4], $m[5], 0, $m[2], $m[3], $m[1]) - ($user->data['user_timezone']) - $contest_data['contest_start'];
			}
			if (!preg_match('#(\\d{4})-(\\d{1,2})-(\\d{1,2}) (\\d{1,2}):(\\d{2})#', $contest_data['contest_end'], $m))
			{
				$errors[] = sprintf($user->lang['CONTEST_END_INVALID'], $contest_data['contest_end']);
				$date_error = true;
			}
			else if (!$start_date_error)
			{
				$contest_data['contest_end'] = gmmktime($m[4], $m[5], 0, $m[2], $m[3], $m[1]) - ($user->data['user_timezone']) - $contest_data['contest_start'];
			}
			if (!$start_date_error && !$date_error)
			{
				if ($contest_data['contest_end'] < $contest_data['contest_rating'])
				{
					$errors[] = $user->lang['CONTEST_END_BEFORE_RATING'];
				}
				if ($contest_data['contest_rating'] < 0)
				{
					$errors[] = $user->lang['CONTEST_RATING_BEFORE_START'];
				}
				if ($contest_data['contest_end'] < 0)
				{
					$errors[] = $user->lang['CONTEST_END_BEFORE_START'];
				}
			}
		}

		// Unset data that are not database fields
		$album_data_sql = $album_data;
		/*
		unset($album_data_sql['album_password_confirm']);
		*/

		// What are we going to do tonight Brain? The same thing we do everynight,
		// try to take over the world ... or decide whether to continue update
		// and if so, whether it's a new album/cat/contest or an existing one
		if (sizeof($errors))
		{
			return $errors;
		}

		/*
		// As we don't know the old password, it's kinda tricky to detect changes
		if ($album_data_sql['album_password_unset'])
		{
			$albumdata_sql['album_password'] = '';
		}
		else if (empty($album_data_sql['album_password']))
		{
			unset($album_data_sql['album_password']);
		}
		else
		{
			$album_data_sql['album_password'] = phpbb_hash($album_data_sql['album_password']);
		}
		unset($album_data_sql['album_password_unset']);
		*/

		if (!isset($album_data_sql['album_id']))
		{
			// no album_id means we're creating a new album
			unset($album_data_sql['type_action']);
			$add_on_top = $request->variable('add_on_top', 0);

			if ($album_data_sql['parent_id'])
			{
				$sql = 'SELECT left_id, right_id, album_type
					FROM ' . $table_prefix . 'gallery_albums
					WHERE album_id = ' . $album_data_sql['parent_id'];
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($user->lang['PARENT_NOT_EXIST'] . $this->back_link($this->u_action . '&amp;parent_id=' . $this->parent_id), E_USER_WARNING);
				}

				if (!$add_on_top)
				{
					$sql = 'UPDATE ' . $table_prefix . 'gallery_albums
						SET left_id = left_id + 2, right_id = right_id + 2
						WHERE album_user_id = 0
							AND left_id > ' . $row['right_id'];
					$db->sql_query($sql);

					$sql = 'UPDATE ' . $table_prefix . 'gallery_albums
						SET right_id = right_id + 2
						WHERE album_user_id = 0
							AND ' . $row['left_id'] . ' BETWEEN left_id AND right_id';
					$db->sql_query($sql);

					$album_data_sql['left_id'] = $row['right_id'];
					$album_data_sql['right_id'] = $row['right_id'] + 1;
				}
				else
				{
					$sql = 'UPDATE ' . $table_prefix . 'gallery_albums
						SET left_id = left_id + 2, right_id = right_id + 2
						WHERE album_user_id = 0
							AND left_id > ' . $row['left_id'];
					$db->sql_query($sql);

					$sql = 'UPDATE ' . $table_prefix . 'gallery_albums
						SET right_id = right_id + 2
						WHERE album_user_id = 0
							AND ' . $row['left_id'] . ' BETWEEN left_id AND right_id';
					$db->sql_query($sql);

					$album_data_sql['left_id'] = $row['left_id'] + 1;
					$album_data_sql['right_id'] = $row['left_id'] + 2;
				}
			}
			else
			{
				if (!$add_on_top)
				{
					$sql = 'SELECT MAX(right_id) AS right_id
						FROM ' . $table_prefix . 'gallery_albums
						WHERE album_user_id = 0';
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					$album_data_sql['left_id'] = $row['right_id'] + 1;
					$album_data_sql['right_id'] = $row['right_id'] + 2;
				}
				else
				{
					$sql = 'UPDATE ' . $table_prefix . 'gallery_albums
						SET left_id = left_id + 2, right_id = right_id + 2
						WHERE album_user_id = 0';
					$db->sql_query($sql);

					$album_data_sql['left_id'] = 1;
					$album_data_sql['right_id'] = 2;
				}
			}

			$sql = 'INSERT INTO ' . $table_prefix . 'gallery_albums ' . $db->sql_build_array('INSERT', $album_data_sql);
			$db->sql_query($sql);
			$album_data['album_id'] = (int) $db->sql_nextid();

			// Type is contest, so create it...
			if ($album_data['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CONTEST)
			{
				$contest_data_sql = $contest_data;
				$contest_data_sql['contest_album_id'] = $album_data['album_id'];
				$contest_data_sql['contest_marked'] = $phpbb_ext_gallery_core_image::IN_CONTEST;

				$sql = 'INSERT INTO ' . $table_prefix . 'gallery_contests ' . $db->sql_build_array('INSERT', $contest_data_sql);
				$db->sql_query($sql);
				$album_data['album_contest'] = (int) $db->sql_nextid();

				$sql = 'UPDATE ' . $table_prefix . 'gallery_albums
					SET album_contest = ' . $album_data['album_contest'] . '
					WHERE album_id = ' . $album_data['album_id'];
				$db->sql_query($sql);
			}
			$log = $phpbb_container->get('phpbbgallery.core.log');

			$log->add_log('admin', 'add', $album_data['album_id'], 0, array('LOG_ALBUM_ADD', $album_data['album_name']));
		}
		else
		{
			$row = $phpbb_ext_gallery_core_album->get_info($album_data_sql['album_id']);
			$reset_marked_images = false;

			if ($row['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CONTEST && $album_data_sql['album_type'] != $phpbb_ext_gallery_core_album::TYPE_CONTEST)
			{
				// Changing a contest to album? No!
				// Changing a contest to category? No!
				$errors[] = $user->lang['ALBUM_WITH_CONTEST_NO_TYPE_CHANGE'];
				return $errors;
			}
			else if ($row['album_type'] != $phpbb_ext_gallery_core_album::TYPE_CONTEST && $album_data_sql['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CONTEST)
			{
				// Changing a album to contest? No!
				// Changing a category to contest? No!
				$errors[] = $user->lang['ALBUM_NO_TYPE_CHANGE_TO_CONTEST'];
				return $errors;
			}
			else if ($row['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CAT && $album_data_sql['album_type'] == $phpbb_ext_gallery_core_album::TYPE_UPLOAD)
			{
				// Changing a category to a album? Yes!
				// Reset the data (you couldn't upload directly in a cat, you must use a album)
				$album_data_sql['album_images'] = $album_data_sql['album_images_real'] = $album_data_sql['album_last_image_id'] = $album_data_sql['album_last_user_id'] = $album_data_sql['album_last_image_time'] = $album_data_sql['album_contest'] = 0;
				$album_data_sql['album_last_username'] = $album_data_sql['album_last_user_colour'] = $album_data_sql['album_last_image_name'] = '';
			}
			else if ($row['album_type'] == $phpbb_ext_gallery_core_album::TYPE_UPLOAD && $album_data_sql['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CAT)
			{
				// Changing a album to a category? Yes!
				// we're turning a uploadable album into a non-uploadable album
				if ($album_data_sql['type_action'] == 'move')
				{
					$to_album_id = $request->variable('to_album_id', 0);

					if ($to_album_id)
					{
						$errors = $this->move_album_content($album_data_sql['album_id'], $to_album_id);
					}
					else
					{
						return array($user->lang['NO_DESTINATION_ALBUM']);
					}
				}
				else if ($album_data_sql['type_action'] == 'delete')
				{
					$errors = $this->delete_album_content($album_data_sql['album_id']);
				}
				else
				{
					return array($user->lang['NO_ALBUM_ACTION']);
				}
			}
			else if ($row['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CONTEST && $album_data_sql['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CONTEST)
			{
				// Changing a contest to contest? Yes!
				// We need to check for the contest_data
				$row_contest = $phpbb_gallery_contest::get_contest($album_data['album_id'], 'album');
				$contest_data['contest_id'] = $row_contest['contest_id'];
				if ($row_contest['contest_marked'] == phpbb_ext_gallery_core_image::NO_CONTEST)
				{
					// If the old contest is finished, but the new one isn't, we need to remark the images!
					// If we change it the other way round, the album.php will do the end on the first visit!
					if (($row_contest['contest_start'] + $row_contest['contest_end']) > time())
					{
						$contest_data['contest_marked'] = phpbb_ext_gallery_core_image::IN_CONTEST;
						$reset_marked_images = true;
					}
				}
			}

			if (sizeof($errors))
			{
				return $errors;
			}

			if ($row['parent_id'] != $album_data_sql['parent_id'])
			{
				if ($row['album_id'] != $album_data_sql['parent_id'])
				{
					$errors = $this->move_album($album_data_sql['album_id'], $album_data_sql['parent_id']);
				}
				else
				{
					$album_data_sql['parent_id'] = $row['parent_id'];
				}
			}

			if (sizeof($errors))
			{
				return $errors;
			}

			unset($album_data_sql['type_action']);

			if ($row['album_name'] != $album_data_sql['album_name'])
			{
				// The album name has changed, clear the parents list of all albums (for safety)
				$sql = 'UPDATE ' . $table_prefix . "gallery_albums 
					SET album_parents = ''";
				$db->sql_query($sql);
			}

			// Setting the album id to the album id is not really received well by some dbs. ;)
			$album_id = $album_data_sql['album_id'];
			unset($album_data_sql['album_id']);

			$sql = 'UPDATE ' . $table_prefix . 'gallery_albums 
				SET ' . $db->sql_build_array('UPDATE', $album_data_sql) . '
				WHERE album_id = ' . $album_id;
			$db->sql_query($sql);

/*			if ($album_data_sql['album_type'] == $phpbb_ext_gallery_core_album::TYPE_CONTEST)
			{
				// Setting the contest id to the contest id is not really received well by some dbs. ;)
				$contest_id = $contest_data['contest_id'];
				unset($contest_data['contest_id']);

				$sql = 'UPDATE ' . GALLERY_CONTESTS_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $contest_data) . '
					WHERE contest_id = ' . $contest_id;
				$db->sql_query($sql);
				if ($reset_marked_images)
				{
					// If the old contest is finished, but the new one isn't, we need to remark the images!
					$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
						SET image_contest_rank = 0,
							image_contest_end = 0,
							image_contest = ' . phpbb_ext_gallery_core_image::IN_CONTEST . '
						WHERE image_album_id = ' . $album_id;
					$db->sql_query($sql);
				}

				// Add it back
				$contest_data['contest_id'] = $contest_id;
			}
*/
			// Add it back
			$album_data['album_id'] = $album_id;

			$log = $phpbb_container->get('phpbbgallery.core.log');

			$log->add_log('admin', 'edit', $album_id, 0, array('LOG_ALBUM_EDIT', $album_data['album_name']));
		}

		return $errors;
	}

	/**
	* Move album
	*
	* borrowed from phpBB3
	* @author: phpBB Group
	* @function: move_forum
	*/
	public function move_album($from_id, $to_id)
	{
		global $db, $user, $table_prefix, $phpbb_container;

		// define support class
		$phpbb_ext_gallery_core_album = $phpbb_container->get('phpbbgallery.core.album');
		$display = $phpbb_container->get('phpbbgallery.core.album.display');
		$phpbb_ext_gallery_core_image = $phpbb_container->get('phpbbgallery.core.image');

		$to_data = $moved_ids = $errors = array();

		// Get the parent data
		if ($to_id > 0)
		{
			$to_data = $phpbb_ext_gallery_core_album->get_info($to_id);
		}

		$moved_albums = $display->get_branch($this->user_id, $from_id, 'children', 'descending');
		$from_data = $moved_albums[0];
		$diff = sizeof($moved_albums) * 2;

		$moved_ids = array();
		for ($i = 0, $end = sizeof($moved_albums); $i < $end; ++$i)
		{
			// Can not select child as parent
			if ($moved_albums[$i]['album_id'] == $to_id)
			{
				return array($user->lang['ALBUM_PARENT_INVALID']);
			}
			$moved_ids[] = $moved_albums[$i]['album_id'];
		}

		// Resync parents
		$sql = 'UPDATE ' . $table_prefix . "gallery_albums
			SET right_id = right_id - $diff, album_parents = ''
			WHERE album_user_id = " . $this->user_id . '
				AND left_id < ' . $from_data['right_id'] . "
				AND right_id > " . $from_data['right_id'];
		$db->sql_query($sql);

		// Resync righthand side of tree
		$sql = 'UPDATE ' . $table_prefix . "gallery_albums
			SET left_id = left_id - $diff, right_id = right_id - $diff, album_parents = ''
			WHERE album_user_id = " . $this->user_id . '
				AND left_id > ' . $from_data['right_id'];
		$db->sql_query($sql);

		if ($to_id > 0)
		{
			// Retrieve $to_data again, it may have been changed...
			$to_data = $phpbb_ext_gallery_core_album->get_info($to_id);

			// Resync new parents
			$sql = 'UPDATE ' . $table_prefix . "gallery_albums
				SET right_id = right_id + $diff, album_parents = ''
				WHERE album_user_id = " . $this->user_id . '
					AND ' . $to_data['right_id'] . ' BETWEEN left_id AND right_id
					AND ' . $db->sql_in_set('album_id', $moved_ids, true);
			$db->sql_query($sql);

			// Resync the righthand side of the tree
			$sql = 'UPDATE ' . $table_prefix . "gallery_albums
				SET left_id = left_id + $diff, right_id = right_id + $diff, album_parents = ''
				WHERE album_user_id = " . $this->user_id . '
					AND left_id > ' . $to_data['right_id'] . '
					AND ' . $db->sql_in_set('album_id', $moved_ids, true);
			$db->sql_query($sql);

			// Resync moved branch
			$to_data['right_id'] += $diff;

			if ($to_data['right_id'] > $from_data['right_id'])
			{
				$diff = '+ ' . ($to_data['right_id'] - $from_data['right_id'] - 1);
			}
			else
			{
				$diff = '- ' . abs($to_data['right_id'] - $from_data['right_id'] - 1);
			}
		}
		else
		{
			$sql = 'SELECT MAX(right_id) AS right_id
				FROM ' . $table_prefix . 'gallery_albums
				WHERE album_user_id = ' . $this->user_id . '
					AND ' . $db->sql_in_set('album_id', $moved_ids, true);
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$diff = '+ ' . ($row['right_id'] - $from_data['left_id'] + 1);
		}

		$sql = 'UPDATE ' . $table_prefix . "gallery_albums
			SET left_id = left_id $diff, right_id = right_id $diff, album_parents = ''
			WHERE album_user_id = " . $this->user_id . '
				AND ' . $db->sql_in_set('album_id', $moved_ids);
		$db->sql_query($sql);

		return $errors;
	}

	/**
	* Remove complete album
	*
	* borrowed from phpBB3
	* @author: phpBB Group
	* @function: delete_forum
	*/
	public function delete_album($album_id, $action_images = 'delete', $action_subalbums = 'delete', $images_to_id = 0, $subalbums_to_id = 0)
	{
		global $db, $user, $cache, $table_prefix, $phpbb_dispatcher, $table_name, $permissions_table, $roles_table, $phpbb_container;

		$phpbb_ext_gallery_core_album = $phpbb_container->get('phpbbgallery.core.album');
		$album_data = $phpbb_ext_gallery_core_album->get_info($album_id);

		$gallery_cache = new \phpbbgallery\core\cache($cache, $db);
		$gallery_user = new \phpbbgallery\core\user($db, $phpbb_dispatcher, $table_name);
		$users_table = $table_prefix . 'gallery_users';
		$phpbb_gallery_auth = $phpbb_container->get('phpbbgallery.core.auth');
		$phpbb_ext_gallery_core_album_display = $phpbb_container->get('phpbbgallery.core.album.display');

		$errors = array();
		$log_action_images = $log_action_albums = $images_to_name = $subalbums_to_name = '';
		$album_ids = array($album_id);

		if ($action_images == 'delete')
		{
			$log_action_images = 'IMAGES';
			$errors = array_merge($errors, $this->delete_album_content($album_id));
		}
		else if ($action_images == 'move')
		{
			if (!$images_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_ALBUM'];
			}
			else
			{
				$log_action_images = 'MOVE_IMAGES';

				$sql = 'SELECT album_name
					FROM ' . $table_prefix . 'gallery_albums
					WHERE album_id = ' . $images_to_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					$errors[] = $user->lang['NO_ALBUM'];
				}
				else
				{
					$images_to_name = $row['album_name'];
					$errors = array_merge($errors, $this->move_album_content($album_id, $images_to_id));
				}
			}
		}

		if (sizeof($errors))
		{
			return $errors;
		}

		if ($action_subalbums == 'delete')
		{
			$log_action_albums = 'ALBUMS';
			$rows = $phpbb_ext_gallery_core_album_display->get_branch($this->user_id, $album_id, 'children', 'descending', false);

			foreach ($rows as $row)
			{
				$album_ids[] = $row['album_id'];
				$errors = array_merge($errors, $this->delete_album_content($row['album_id']));
			}

			if (sizeof($errors))
			{
				return $errors;
			}

			$diff = sizeof($album_ids) * 2;

			$sql = 'DELETE FROM ' . $table_prefix . 'gallery_albums
				WHERE ' . $db->sql_in_set('album_id', $album_ids);
			$db->sql_query($sql);
		}
		else if ($action_subalbums == 'move')
		{
			if (!$subalbums_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_ALBUM'];
			}
			else
			{
				$log_action_albums = 'MOVE_ALBUMS';

				$sql = 'SELECT album_name
					FROM ' . $table_prefix . 'gallery_albums
					WHERE album_id = ' . $subalbums_to_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					$errors[] = $user->lang['NO_ALBUM'];
				}
				else
				{
					$subalbums_to_name = $row['album_name'];

					$sql = 'SELECT album_id
						FROM ' . $table_prefix . "gallery_albums
						WHERE parent_id = $album_id";
					$result = $db->sql_query($sql);

					while ($row = $db->sql_fetchrow($result))
					{
						$this->move_album($row['album_id'], $subalbums_to_id);
					}
					$db->sql_freeresult($result);

					// Grab new album data for correct tree updating later
					$album_data = $phpbb_ext_gallery_core_album->get_info($album_id);

					$sql = 'UPDATE ' . $table_prefix . "gallery_albums
						SET parent_id = $subalbums_to_id
						WHERE parent_id = $album_id
							AND album_user_id = " . $this->user_id;
					$db->sql_query($sql);

					$diff = 2;
					$sql = 'DELETE FROM ' . $table_prefix . "gallery_albums
						WHERE album_id = $album_id";
					$db->sql_query($sql);
				}
			}

			if (sizeof($errors))
			{
				return $errors;
			}
		}
		else
		{
			$diff = 2;
			$sql = 'DELETE FROM ' . $table_prefix . "gallery_albums 
				WHERE album_id = $album_id";
			$db->sql_query($sql);
		}

		// Resync tree
		$sql = 'UPDATE ' . $table_prefix . "gallery_albums 
			SET right_id = right_id - $diff
			WHERE left_id < {$album_data['right_id']} AND right_id > {$album_data['right_id']}
				AND album_user_id = " . $this->user_id;
		$db->sql_query($sql);

		$sql = 'UPDATE ' . $table_prefix . "gallery_albums 
			SET left_id = left_id - $diff, right_id = right_id - $diff
			WHERE left_id > {$album_data['right_id']}
				AND album_user_id = " . $this->user_id;
		$db->sql_query($sql);

		$log_action = implode('_', array($log_action_images, $log_action_albums));

		/**
		* Log what we did
		*/
		$log = $phpbb_container->get('phpbbgallery.core.log');

		switch ($log_action)
		{
			case 'MOVE_IMAGES_MOVE_ALBUMS':
				$log->add_log('admin', 'del', 0, 0, array('LOG_ALBUM_DEL_MOVE_IMAGES_MOVE_ALBUMS', $images_to_name, $subalbums_to_name, $album_data['album_name']));
			break;

			case 'MOVE_IMAGES_ALBUMS':
				$log->add_log('admin', 'del', $images_to_id, 0, array('LOG_ALBUM_DEL_MOVE_IMAGES_ALBUMS', $images_to_name, $album_data['album_name']));
			break;

			case 'IMAGES_MOVE_ALBUMS':
				$log->add_log('admin', 'del', $subalbums_to_id, 0, array('LOG_ALBUM_DEL_IMAGES_MOVE_ALBUMS', $subalbums_to_name, $album_data['album_name']));
			break;

			case '_MOVE_ALBUMS':
				$log->add_log('admin', 'del', $subalbums_to_id, 0, array('LOG_ALBUM_DEL_MOVE_ALBUMS', $subalbums_to_name, $album_data['album_name']));
			break;

			case 'MOVE_IMAGES_':
				$log->add_log('admin', 'del', $images_to_id, 0, array('LOG_ALBUM_DEL_MOVE_IMAGES', $images_to_name, $album_data['album_name']));
			break;

			case 'IMAGES_ALBUMS':
				$log->add_log('admin', 'del', 0, 0, array('LOG_ALBUM_DEL_IMAGES_ALBUMS', $album_data['album_name']));
			break;

			case '_ALBUMS':
				$log->add_log('admin', 'del', 0, 0, array('LOG_ALBUM_DEL_ALBUMS', $album_data['album_name']));
			break;

			case 'IMAGES_':
				$log->add_log('admin', 'del', 0, 0, array('LOG_ALBUM_DEL_IMAGES', $album_data['album_name']));
			break;

			default:
				$log->add_log('admin', 'del', 0, 0, array('LOG_ALBUM_DEL_ALBUM', $album_data['album_name']));
			break;
		}

		$phpbb_gallery_auth->set_user_permissions('all', '');
		return $errors;
	}

	/**
	* Move album content from one to another album
	*
	* borrowed from phpBB3
	* @author: phpBB Group
	* @function: move_forum_content
	*/
	public function move_album_content($from_id, $to_id, $sync = true)
	{
		global $cache, $db, $phpbb_dispatcher, $table_prefix, $config, $phpbb_container;

		$phpbb_ext_gallery_core_image = $phpbb_container->get('phpbbgallery.core.image');
		$phpbb_gallery_notification = new \phpbbgallery\core\notification();
		$phpbb_gallery_config = $phpbb_container->get('phpbbgallery.core.config');
		$phpbb_gallery_report = $phpbb_container->get('phpbbgallery.core.report');
		$phpbb_ext_gallery_core_album = $phpbb_container->get('phpbbgallery.core.album');
		// Lucifer TODO - Log to gallery log
		//$sql = 'UPDATE ' . LOG_TABLE . "
		//	SET album_id = $to_id
		//	WHERE album_id = $from_id
		//		AND log_type = " . LOG_GALLERY;
		//$db->sql_query($sql);

		// Reset contest-information for safety.
		$sql = 'UPDATE ' . $table_prefix . 'gallery_images
			SET image_album_id = ' . $to_id . ',
				image_contest_rank = 0,
				image_contest_end = 0,
				image_contest = ' . $phpbb_ext_gallery_core_image::NO_CONTEST . '
			WHERE image_album_id = ' . $from_id;
		$db->sql_query($sql);

		$phpbb_gallery_report->move_album_content($from_id, $to_id);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_contests
			WHERE contest_album_id = ' . $from_id;
		$db->sql_query($sql);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_permissions
			WHERE perm_album_id = ' . $from_id;
		$db->sql_query($sql);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_modscache
			WHERE album_id = ' . $from_id;
		$db->sql_query($sql);

		$phpbb_gallery_notification->delete_albums($from_id);

		/**
		* Event related to moving album content
		*
		* @event phpbbgallery.core.album.manage.move_album_content
		* @var	int	from_id		Album we are moving from
		* @var	int	to_id		Album we are moving to
		* @var	bool	sync	Should we sync the albums data
		* @since 1.2.0
		*/
		$vars = array('from_id', 'to_id', 'sync');
		extract($phpbb_dispatcher->trigger_event('phpbbgallery.core.album.manage.move_album_content', compact($vars)));

		$cache->destroy('sql', $table_prefix . 'gallery_albums');
		$cache->destroy('sql', $table_prefix . 'gallery_comments');
		$cache->destroy('sql', $table_prefix . 'gallery_images');
		$cache->destroy('sql', $table_prefix . 'gallery_rates');
		$cache->destroy('sql', $table_prefix . 'gallery_reports');
		$cache->destroy('sql', $table_prefix . 'gallery_watch');
		$cache->destroy('_albums');

		if ($sync)
		{
			// Resync counters
			$phpbb_ext_gallery_core_album->update_info($from_id);
			$phpbb_ext_gallery_core_album->update_info($to_id);
		}

		return array();
	}

	/**
	* Delete album content:
	* Deletes all images, comments, rates, image-files, etc.
	*/
	public function delete_album_content($album_id)
	{
		global $cache, $db, $phpbb_dispatcher, $table_prefix, $config, $phpbb_container;
		$phpbb_ext_gallery_core_image = $phpbb_container->get('phpbbgallery.core.image');
		$phpbb_gallery_notification = new \phpbbgallery\core\notification();
		$phpbb_gallery_user = $phpbb_container->get('phpbbgallery.core.user');
		$phpbb_gallery_config = $phpbb_container->get('phpbbgallery.core.config');
		$album_id = (int) $album_id;

		// Before we remove anything we make sure we are able to adjust the image counts later. ;)
		$sql = 'SELECT image_user_id
			FROM ' . $table_prefix . 'gallery_images
			WHERE image_album_id = ' . $album_id . '
				AND image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . '
				AND image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_ORPHAN;
		$result = $db->sql_query($sql);

		$image_counts = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$image_counts[$row['image_user_id']] = (!empty($image_counts[$row['image_user_id']])) ? $image_counts[$row['image_user_id']] + 1 : 1;
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT image_id, image_filename, image_album_id
			FROM ' . $table_prefix . 'gallery_images
			WHERE image_album_id = ' . $album_id;
		$result = $db->sql_query($sql);

		$filenames = $deleted_images = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$deleted_images[] = $row['image_id'];
			$filenames[(int) $row['image_id']] = $row['image_filename'];
		}
		$db->sql_freeresult($result);

		if (!empty($deleted_images))
		{
			$phpbb_ext_gallery_core_image->delete_images($deleted_images, $filenames);
		}

		// Lucifer TODO: Log Gallery deletion from log
		//$sql = 'DELETE FROM ' . LOG_TABLE . "
		//	WHERE album_id = $album_id
		//		AND log_type = " . LOG_GALLERY;
		//$db->sql_query($sql);

		//@todo: merge queries into loop
		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_permissions
			WHERE perm_album_id = ' . $album_id;
		$db->sql_query($sql);
		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_contests
			WHERE contest_album_id = ' . $album_id;
		$db->sql_query($sql);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_modscache
			WHERE album_id = ' . $album_id;
		$db->sql_query($sql);

		$phpbb_gallery_notification->delete_albums($album_id);

		// Adjust users image counts
		if (!empty($image_counts))
		{
			foreach ($image_counts as $image_user_id => $substract)
			{
				$phpbb_gallery_user->set_user_id($image_user_id);
				$phpbb_gallery_user->update_images((0 - $substract));
			}
		}

		// Make sure the overall image & comment count is correct...
		$sql = 'SELECT COUNT(image_id) AS num_images, SUM(image_comments) AS num_comments
			FROM ' . $table_prefix . 'gallery_images 
			WHERE image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_UNAPPROVED . '
				AND image_status <> ' . $phpbb_ext_gallery_core_image::STATUS_ORPHAN;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$phpbb_gallery_config->set('num_images', $row['num_images']);
		$phpbb_gallery_config->set('num_comments', $row['num_comments']);

		/**
		* Event delete album content
		*
		* @event phpbbgallery.core.album.manage.delete_album_content
		* @var	int	album_id		Album we are deleting
		* @since 1.2.0
		*/
		$vars = array('album_id');
		extract($phpbb_dispatcher->trigger_event('phpbbgallery.core.album.manage.delete_album_content', compact($vars)));

		$cache->destroy('sql', $table_prefix . 'gallery_albums');
		$cache->destroy('sql', $table_prefix . 'gallery_comments');
		$cache->destroy('sql', $table_prefix . 'gallery_images');
		$cache->destroy('sql', $table_prefix . 'gallery_rates');
		$cache->destroy('sql', $table_prefix . 'gallery_reports');
		$cache->destroy('sql', $table_prefix . 'gallery_watch');
		$cache->destroy('_albums');

		return array();
	}

	/**
	* Move album position by $steps up/down
	*
	* borrowed from phpBB3
	* @author: phpBB Group
	* @function: move_forum_by
	*/
	public function move_album_by($album_row, $action = 'move_up', $steps = 1)
	{
		global $db, $table_prefix;

		/**
		* Fetch all the siblings between the module's current spot
		* and where we want to move it to. If there are less than $steps
		* siblings between the current spot and the target then the
		* module will move as far as possible
		*/
		$sql = 'SELECT album_id, album_name, left_id, right_id
			FROM ' . $table_prefix . "gallery_albums
			WHERE parent_id = {$album_row['parent_id']}
				AND album_user_id = " . $this->user_id . '
				AND ' . (($action == 'move_up') ? "right_id < {$album_row['right_id']} ORDER BY right_id DESC" : "left_id > {$album_row['left_id']} ORDER BY left_id ASC");
		$result = $db->sql_query_limit($sql, $steps);

		$target = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$target = $row;
		}
		$db->sql_freeresult($result);

		if (!sizeof($target))
		{
			// The album is already on top or bottom
			return false;
		}

		/**
		* $left_id and $right_id define the scope of the nodes that are affected by the move.
		* $diff_up and $diff_down are the values to substract or add to each node's left_id
		* and right_id in order to move them up or down.
		* $move_up_left and $move_up_right define the scope of the nodes that are moving
		* up. Other nodes in the scope of ($left_id, $right_id) are considered to move down.
		*/
		if ($action == 'move_up')
		{
			$left_id = $target['left_id'];
			$right_id = $album_row['right_id'];

			$diff_up = $album_row['left_id'] - $target['left_id'];
			$diff_down = $album_row['right_id'] + 1 - $album_row['left_id'];

			$move_up_left = $album_row['left_id'];
			$move_up_right = $album_row['right_id'];
		}
		else
		{
			$left_id = $album_row['left_id'];
			$right_id = $target['right_id'];

			$diff_up = $album_row['right_id'] + 1 - $album_row['left_id'];
			$diff_down = $target['right_id'] - $album_row['right_id'];

			$move_up_left = $album_row['right_id'] + 1;
			$move_up_right = $target['right_id'];
		}

		// Now do the dirty job
		$sql = 'UPDATE ' . $table_prefix . "gallery_albums
			SET left_id = left_id + CASE
				WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END,
			right_id = right_id + CASE
				WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END,
			album_parents = ''
			WHERE
				left_id BETWEEN {$left_id} AND {$right_id}
				AND right_id BETWEEN {$left_id} AND {$right_id}
				AND album_user_id = " . $this->user_id;
		$db->sql_query($sql);

		return $target['album_name'];
	}
}
