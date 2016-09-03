<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace phpbbgallery\acpcleanup;

class cleanup
{
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbbgallery\core\file\file $tool, \phpbbgallery\core\block $block, \phpbbgallery\core\comment $comment,
	\phpbbgallery\core\config $gallery_config, \phpbbgallery\core\log $log, \phpbbgallery\core\moderate $moderate,
	$albums_table, $images_table)
	{
		$this->db = $db;
		$this->tool = $tool;
		$this->block = $block;
		$this->comment = $comment;
		$this->gallery_config = $gallery_config;
		$this->log = $log;
		$this->moderate = $moderate;
		$this->albums_table = $albums_table;
		$this->images_table = $images_table;
	}
	/**
	* Delete source files without a database entry.
	*
	* @param	array	$filenames		An array of filenames
	* @return	string	Language key for the success message.
	*/
	public function delete_files($filenames)
	{
		foreach ($filenames as $file)
		{
			$this->tool->delete(utf8_decode($file));
			$this->tool->delete_cache(utf8_decode($file));
		}
		$this->log->add_log('admin', 'clean_deletefiles', 0, 0, array('LOG_CLEANUP_DELETE_FILES', count($filenames)));
		return 'CLEAN_ENTRIES_DONE';
	}

	/**
	* Delete images, where the source file is missing.
	*
	* @param	mixed	$image_ids		Either an array of integers or an integer.
	* @return	string	Language key for the success message.
	*/
	public function delete_images($image_ids)
	{
		$this->log->add_log('admin', 'clean_deleteentries', 0, 0, array('LOG_CLEANUP_DELETE_ENTRIES', count($image_ids)));
		$this->moderate->delete_images($image_ids, false, true, true);

		return 'CLEAN_SOURCES_DONE';
	}

	/**
	* Delete images, where the author is missing.
	*
	* @param	mixed	$image_ids		Either an array of integers or an integer.
	* @return	string	Language key for the success message.
	*/
	public function delete_author_images($image_ids)
	{
		$this->log->add_log('admin', 'clean_deletenoauthors', 0, 0, array('LOG_CLEANUP_DELETE_NO_AUTHOR', count($image_ids)));
		$this->moderate->delete_images($image_ids);

		return 'CLEAN_AUTHORS_DONE';
	}

	/**
	* Delete comments, where the author is missing.
	*
	* @param	mixed	$comment_ids	Either an array of integers or an integer.
	* @return	string	Language key for the success message.
	*/
	public function delete_author_comments($comment_ids)
	{
		$this->log->add_log('admin', 'clean_deletecna', 0, 0, array('LOG_CLEANUP_COMMENT_DELETE_NO_AUTHOR', count($comment_ids)));
		$this->comment->delete_comments($comment_ids);

		return 'CLEAN_COMMENTS_DONE';
	}

	/**
	* Delete unwanted and obsolent personal galleries.
	*
	* @param	array	$unwanted_pegas		User IDs we want to delete the pegas.
	* @param	array	$obsolent_pegas		User IDs we want to delete the pegas.
	* @return	array	Language keys for the success messages.
	*/
	public function delete_pegas($unwanted_pegas, $obsolent_pegas)
	{

		$delete_pegas = array_merge($unwanted_pegas, $obsolent_pegas);

		$delete_images = $delete_albums = $user_image_count = array();
		$num_pegas = 0;

		$sql = 'SELECT album_id, parent_id
			FROM ' . $this->albums_table . '
			WHERE ' . $this->db->sql_in_set('album_user_id', $delete_pegas);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$delete_albums[] = (int) $row['album_id'];
			if ($row['parent_id'] == 0)
			{
				$num_pegas++;
			}
		}
		$this->db->sql_freeresult($result);

		$sql = 'SELECT image_id, image_filename, image_status, image_user_id
			FROM ' . $this->images_table . '
			WHERE ' . $this->db->sql_in_set('image_album_id', $delete_albums, false, true);
		$result = $this->db->sql_query($sql);

		$filenames = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$delete_images[] = (int) $row['image_id'];
			$filenames[(int) $row['image_id']] = $row['image_filename'];

			if (($row['image_status'] == $this->block->get_status_unaproved()) ||
			($row['image_status'] == $this->block->get_image_status_orphan()))
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
		$this->db->sql_freeresult($result);

		if (!empty($delete_images))
		{
			$this->moderate->delete_images($delete_images, $filenames);
		}

		$sql = 'DELETE FROM ' . $this->albums_table . '
			WHERE ' . $this->db->sql_in_set('album_id', $delete_albums);
		$this->db->sql_query($sql);
		$this->gallery_config->dec('num_pegas', $num_pegas);

		if (in_array($this->gallery_config->get('newest_pega_album_id'), $delete_albums))
		{
			// Update the config for the statistic on the index
			if ($this->gallery_config->get('num_pegas') > 0)
			{
				$sql_array = array(
					'SELECT'		=> 'a.album_id, u.user_id, u.username, u.user_colour',
					'FROM'			=> array($this->albums_table => 'a'),

					'LEFT_JOIN'		=> array(
						array(
							'FROM'		=> array(USERS_TABLE => 'u'),
							'ON'		=> 'u.user_id = a.album_user_id',
						),
					),

					'WHERE'			=> 'a.album_user_id <> ' . $this->album->get_public() . ' AND a.parent_id = 0',
					'ORDER_BY'		=> 'a.album_id DESC',
				);
				$sql = $this->db->sql_build_query('SELECT', $sql_array);

				$result = $this->db->sql_query_limit($sql, 1);
				$newest_pega = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);
			}

			if (($this->gallery_config->get('num_pegas') > 0) && isset($newest_pega))
			{
				$this->gallery_config->set('newest_pega_user_id', $newest_pega['user_id']);
				$this->gallery_config->set('newest_pega_username', $newest_pega['username']);
				$this->gallery_config->set('newest_pega_user_colour', $newest_pega['user_colour']);
				$this->gallery_config->set('newest_pega_album_id', $newest_pega['album_id']);
			}
			else
			{
				$this->gallery_config->set('newest_pega_user_id', 0);
				$this->gallery_config->set('newest_pega_username', '');
				$this->gallery_config->set('newest_pega_user_colour', '');
				$this->gallery_config->set('newest_pega_album_id', 0);

				if (isset($newest_pega))
				{
					$this->gallery_config->set('num_pegas', 0);
				}
			}
		}
/*
		foreach ($user_image_count as $user_id => $images)
		{
			//phpbb_gallery_hookup::add_image($user_id, (0 - $images));

			$uploader = new \phpbbgallery\core\user($this->db, $user_id, false);
			$uploader->update_images((0 - $images));
		}
		\phpbbgallery\core\user::update_users($delete_pegas, array('personal_album_id' => 0));
*/
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
	public function prune($pattern)
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
			FROM ' . $this->images_table . '
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
			$this->moderate->delete_images($image_ids, $filenames);
		}

		return 'CLEAN_PRUNE_DONE';
	}

	/**
	*
	*/
	public function lang_prune_pattern($pattern)
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
						FROM ' . $this->albums_table . '
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
						$value .= (($value) ? ', ' : '') . get_username_string('full', $row['user_id'], (($row['user_id'] != ANONYMOUS) ? $row['username'] : $user->lang('GUEST')), $row['user_colour']);
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
