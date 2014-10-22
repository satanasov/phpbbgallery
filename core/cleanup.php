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

if (!defined('IN_PHPBB'))
{
	exit;
}

class phpbb_ext_gallery_core_cleanup
{
	/**
	* Delete source files without a database entry.
	*
	* @param	array	$filenames		An array of filenames
	* @return	string	Language key for the success message.
	*/
	static public function delete_files($filenames)
	{
		foreach ($filenames as $file)
		{
			phpbb_gallery_image_file::delete(utf8_decode($file));
		}

		return 'CLEAN_ENTRIES_DONE';
	}

	/**
	* Delete images, where the source file is missing.
	*
	* @param	mixed	$image_ids		Either an array of integers or an integer.
	* @return	string	Language key for the success message.
	*/
	static public function delete_images($image_ids)
	{
		phpbb_gallery_image::delete_images($image_ids, false, true, true);

		return 'CLEAN_SOURCES_DONE';
	}

	/**
	* Delete images, where the author is missing.
	*
	* @param	mixed	$image_ids		Either an array of integers or an integer.
	* @return	string	Language key for the success message.
	*/
	static public function delete_author_images($image_ids)
	{
		phpbb_gallery_image::delete_images($image_ids);

		return 'CLEAN_AUTHORS_DONE';
	}

	/**
	* Delete comments, where the author is missing.
	*
	* @param	mixed	$comment_ids	Either an array of integers or an integer.
	* @return	string	Language key for the success message.
	*/
	static public function delete_author_comments($comment_ids)
	{
		phpbb_gallery_comment::delete_comments($comment_ids);

		return 'CLEAN_COMMENTS_DONE';
	}

	/**
	* Delete unwanted and obsolent personal galleries.
	*
	* @param	array	$unwanted_pegas		User IDs we want to delete the pegas.
	* @param	array	$obsolent_pegas		User IDs we want to delete the pegas.
	* @return	array	Language keys for the success messages.
	*/
	static public function delete_pegas($unwanted_pegas, $obsolent_pegas)
	{
		global $db;

		$delete_pegas = array_merge($unwanted_pegas, $obsolent_pegas);

		$delete_images = $delete_albums = $user_image_count = array();
		$num_pegas = 0;

		$sql = 'SELECT album_id, parent_id
			FROM ' . GALLERY_ALBUMS_TABLE . '
			WHERE ' . $db->sql_in_set('album_user_id', $delete_pegas);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$delete_albums[] = (int) $row['album_id'];
			if ($row['parent_id'] == 0)
			{
				$num_pegas++;
			}
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT image_id, image_filename, image_status, image_user_id
			FROM ' . GALLERY_IMAGES_TABLE . '
			WHERE ' . $db->sql_in_set('image_album_id', $delete_albums, false, true);
		$result = $db->sql_query($sql);

		$filenames = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$delete_images[] = (int) $row['image_id'];
			$filenames[(int) $row['image_id']] = $row['image_filename'];

			if (($row['image_status'] == phpbb_gallery_image::STATUS_UNAPPROVED) ||
			 ($row['image_status'] == phpbb_gallery_image::STATUS_ORPHAN))
			{
				continue;
			}

			if (isset($user_image_count[(int) $row['image_user_id']]))
			{
				$user_image_count[(int) $row['image_user_id']]++;
			}
			else
			{
				$user_image_count[(int) $row['image_user_id']] = 1;
			}
		}
		$db->sql_freeresult($result);

		if (!empty($delete_images))
		{
			phpbb_gallery_image::delete_images($delete_images, $filenames);
		}

		$sql = 'DELETE FROM ' . GALLERY_ALBUMS_TABLE . '
			WHERE ' . $db->sql_in_set('album_id', $delete_albums);
		$db->sql_query($sql);
		phpbb_gallery_config::dec('num_pegas', $num_pegas);

		if (in_array(phpbb_gallery_config::get('newest_pega_album_id'), $delete_albums))
		{
			// Update the config for the statistic on the index
			if (phpbb_gallery_config::get('num_pegas') > 0)
			{
				$sql_array = array(
					'SELECT'		=> 'a.album_id, u.user_id, u.username, u.user_colour',
					'FROM'			=> array(GALLERY_ALBUMS_TABLE => 'a'),

					'LEFT_JOIN'		=> array(
						array(
							'FROM'		=> array(USERS_TABLE => 'u'),
							'ON'		=> 'u.user_id = a.album_user_id',
						),
					),

					'WHERE'			=> 'a.album_user_id <> ' . phpbb_gallery_album::PUBLIC_ALBUM . ' AND a.parent_id = 0',
					'ORDER_BY'		=> 'a.album_id DESC',
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);

				$result = $db->sql_query_limit($sql, 1);
				$newest_pega = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
			}

			if ((phpbb_gallery_config::get('num_pegas') > 0) && isset($newest_pega))
			{
				phpbb_gallery_config::set('newest_pega_user_id', $newest_pega['user_id']);
				phpbb_gallery_config::set('newest_pega_username', $newest_pega['username']);
				phpbb_gallery_config::set('newest_pega_user_colour', $newest_pega['user_colour']);
				phpbb_gallery_config::set('newest_pega_album_id', $newest_pega['album_id']);
			}
			else
			{
				phpbb_gallery_config::set('newest_pega_user_id', 0);
				phpbb_gallery_config::set('newest_pega_username', '');
				phpbb_gallery_config::set('newest_pega_user_colour', '');
				phpbb_gallery_config::set('newest_pega_album_id', 0);

				if (isset($newest_pega))
				{
					phpbb_gallery_config::set('num_pegas', 0);
				}
			}
		}

		foreach ($user_image_count as $user_id => $images)
		{
			phpbb_gallery_hookup::add_image($user_id, (0 - $images));

			$uploader = new phpbb_gallery_user($db, $user_id, false);
			$uploader->update_images((0 - $images));
		}
		phpbb_gallery_user::update_users($delete_pegas, array('personal_album_id' => 0));

		$return = array();
		if ($obsolent_pegas)
		{
			$return[] = 'CLEAN_PERSONALS_DONE';
		}
		if ($unwanted_pegas)
		{
			$return[] = 'CLEAN_PERSONALS_BAD_DONE';
		}

		return $return;
	}

	/**
	*
	*/
	static public function prune($pattern)
	{
		global $db;

		$sql_where = '';
		if (isset($pattern['image_album_id']))
		{
			$pattern['image_album_id'] = array_map('intval', explode(',', $pattern['image_album_id']));
		}
		if (isset($pattern['image_user_id']))
		{
			$pattern['image_user_id'] = array_map('intval', explode(',', $pattern['image_user_id']));
		}
		foreach ($pattern as $field => $value)
		{
			if (is_array($value))
			{
				$sql_where .= (($sql_where) ? ' AND ' : ' WHERE ') . $db->sql_in_set($field, $value);
				continue;
			}
			$sql_where .= (($sql_where) ? ' AND ' : ' WHERE ') . $field . ' < ' . $value;
		}

		$sql = 'SELECT image_id, image_filename
			FROM ' . GALLERY_IMAGES_TABLE . '
			' . $sql_where;
		$result = $db->sql_query($sql);
		$image_ids = $filenames = $update_albums = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$image_ids[] = (int) $row['image_id'];
			$filenames[(int) $row['image_id']] = $row['image_filename'];
		}
		$db->sql_freeresult($result);

		if ($image_ids)
		{
			phpbb_gallery_image::delete_images($image_ids, $filenames);
		}

		return 'CLEAN_PRUNE_DONE';
	}

	/**
	*
	*/
	static public function lang_prune_pattern($pattern)
	{
		global $db, $user;

		if (isset($pattern['image_album_id']))
		{
			$pattern['image_album_id'] = array_map('intval', explode(',', $pattern['image_album_id']));
		}
		if (isset($pattern['image_user_id']))
		{
			$pattern['image_user_id'] = array_map('intval', explode(',', $pattern['image_user_id']));
		}

		$lang_pattern = '';
		foreach ($pattern as $field => $value)
		{
			$field = (strpos($field, 'image_') === 0) ? substr($field, 6) : $field;

			switch ($field)
			{
				case 'album_id':
					$sql = 'SELECT album_name
						FROM ' . GALLERY_ALBUMS_TABLE . '
						WHERE ' . $db->sql_in_set('album_id', $value) . '
						ORDER BY album_id ASC';
					$result = $db->sql_query($sql);
					$value = '';
					while ($row = $db->sql_fetchrow($result))
					{
						$value .= (($value) ? ', ' : '') . $row['album_name'];
					}
					$db->sql_freeresult($result);
				break;

				case 'user_id':
					$sql = 'SELECT user_id, user_colour, username
						FROM ' . USERS_TABLE . '
						WHERE ' . $db->sql_in_set('user_id', $value) . '
						ORDER BY user_id ASC';
					$result = $db->sql_query($sql);
					$value = '';
					while ($row = $db->sql_fetchrow($result))
					{
						$value .= (($value) ? ', ' : '') . get_username_string('full', $row['user_id'], (($row['user_id'] != ANONYMOUS) ? $row['username'] : $user->lang['GUEST']), $row['user_colour']);
					}
					$db->sql_freeresult($result);
				break;

				case 'time':
					$value = $user->format_date($value, false, true);
				break;

				case 'rate_avg':
					$value = ($value / 100);
				break;
			}
			$lang_pattern .= (($lang_pattern) ? '<br />' : '') . $user->lang('PRUNE_PATTERN_' . strtoupper($field), $value);
		}

		return $lang_pattern;
	}
}
