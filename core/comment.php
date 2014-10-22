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

class phpbb_ext_gallery_core_comment
{
	/**
	* Is the user allowed to comment?
	* Following statements must be true:
	*	- User must have permissions.
	*	- User is neither owner of the image nor guest.
	*	- Album and image are not locked.
	*
	* @return	bool
	*/
	static public function is_allowed($album_data, $image_data)
	{
		global $user, $phpbb_ext_gallery;

		return $phpbb_ext_gallery->config->get('allow_comments') && (!$phpbb_ext_gallery->config->get('comment_user_control') || $image_data['image_allow_comments']) &&
			($phpbb_ext_gallery->auth->acl_check('m_status', $album_data['album_id'], $album_data['album_user_id']) ||
			 (($image_data['image_status'] == phpbb_gallery_image::STATUS_APPROVED) && ($album_data['album_status'] != phpbb_ext_gallery_core_album::STATUS_LOCKED)));
	}

	/**
	* Is the user able to comment?
	* Following statements must be true:
	*	- User must be allowed to rate
	*	- If the image is in a contest, it must be finished
	*
	* @return	bool
	*/
	static public function is_able($album_data, $image_data)
	{
		global $user;
		return self::is_allowed($album_data, $image_data) && phpbb_ext_gallery_core_contest::is_step('comment', $album_data);
	}

	/**
	* Add a comment
	*/
	static public function add($data, $comment_username = '')
	{
		global $db, $user, $phpbb_ext_gallery;

		if (!isset($data['comment_image_id']) || !isset($data['comment']))
		{
			return;
		}

		$data = $data + array(
			'comment_user_id'		=> $user->data['user_id'],
			'comment_username'		=> ($user->data['user_id'] != ANONYMOUS) ? $user->data['username'] : $comment_username,
			'comment_user_colour'	=> $user->data['user_colour'],
			'comment_user_ip'		=> $user->ip,
			'comment_time'			=> time(),
		);

		$db->sql_query('INSERT INTO ' . GALLERY_COMMENTS_TABLE . ' ' . $db->sql_build_array('INSERT', $data));
		$newest_comment_id = (int) $db->sql_nextid();
		$phpbb_ext_gallery->config->inc('num_comments', 1);

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . "
			SET image_comments = image_comments + 1,
				image_last_comment = $newest_comment_id
			WHERE image_id = " . (int) $data['comment_image_id'];
		$db->sql_query($sql);

		return $newest_comment_id;
	}

	/**
	* Edit comment
	*/
	static public function edit($comment_id, $data)
	{
		global $db, $user;

		if (!isset($data['comment']))
		{
			return;
		}

		$data = $data + array(
			'comment_edit_time'		=> time(),
			'comment_edit_user_id'	=> $user->data['user_id'],
		);

		$sql = 'UPDATE ' . GALLERY_COMMENTS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $data) . '
			WHERE comment_id = ' . (int) $comment_id;
		$db->sql_query($sql);

		return true;
	}

	/**
	* Sync last comment information
	*/
	static public function sync_image_comments($image_ids = false)
	{
		global $db;

		$sql_where = $sql_where_image = '';
		$resync = array();
		if ($image_ids != false)
		{
			$image_ids = self::cast_mixed_int2array($image_ids);
			$sql_where = 'WHERE ' . $db->sql_in_set('comment_image_id', $image_ids);
			$sql_where_image = 'WHERE ' . $db->sql_in_set('image_id', $image_ids);
		}

		$sql = 'SELECT comment_image_id, COUNT(comment_id) AS num_comments, MAX(comment_id) AS last_comment
			FROM ' . GALLERY_COMMENTS_TABLE . "
			$sql_where
			GROUP BY comment_image_id
			ORDER BY comment_id DESC";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$resync[$row['comment_image_id']] = array(
				'last_comment'	=> $row['last_comment'],
				'num_comments'	=> $row['num_comments'],
			);
		}
		$db->sql_freeresult($result);

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_last_comment = 0,
				image_comments = 0
			' . $sql_where_image;
		$db->sql_query($sql);

		if (!empty($resync))
		{
			foreach ($resync as $image_id => $data)
			{
				$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
					SET image_last_comment = ' . $data['last_comment'] . ',
						image_comments = ' . $data['num_comments'] . '
					WHERE image_id = ' . $image_id;
				$db->sql_query($sql);
			}
		}
	}

	/**
	* Delete comments
	*
	* @param	mixed	$comment_ids	Array or integer with comment_id we delete.
	*/
	static public function delete_comments($comment_ids)
	{
		global $db;

		$comment_ids = self::cast_mixed_int2array($comment_ids);

		$sql = 'SELECT comment_image_id, COUNT(comment_id) AS num_comments
			FROM ' . GALLERY_COMMENTS_TABLE . '
			WHERE ' . $db->sql_in_set('comment_id', $comment_ids) . '
			GROUP BY comment_image_id';
		$result = $db->sql_query($sql);

		$image_ids = array();
		$total_comments = 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$image_ids[] = (int) $row['comment_image_id'];
			$total_comments += $row['num_comments'];
		}
		$db->sql_freeresult($result);

		$sql = 'DELETE FROM ' . GALLERY_COMMENTS_TABLE . '
			WHERE ' . $db->sql_in_set('comment_id', $comment_ids);
		$db->sql_query($sql);

		self::sync_image_comments($image_ids);

		$phpbb_ext_gallery->config->dec('num_comments', $total_comments);
	}

	/**
	* Delete comments for given image_ids
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete the comments.
	* @param	bool	$reset_stats	Shall we also reset the statistics? We can save that query, when the images are deleted anyway.
	*/
	static public function delete_images($image_ids, $reset_stats = false)
	{
		global $db;

		$image_ids = self::cast_mixed_int2array($image_ids);

		$sql = 'DELETE FROM ' . GALLERY_COMMENTS_TABLE . '
			WHERE ' . $db->sql_in_set('comment_image_id', $image_ids);
		$db->sql_query($sql);

		if ($reset_stats)
		{
			$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
				SET image_comments = 0
					image_last_comment = 0
				WHERE ' . $db->sql_in_set('image_id', $image_ids);
			$db->sql_query($sql);
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
