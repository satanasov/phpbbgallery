<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace phpbbgallery\core;

class comment
{
	/**
	 * Constructor
	 *
	 * @param \phpbb\user                       $user
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbbgallery\core\config         $config
	 * @param \phpbbgallery\core\auth\auth      $auth
	 * @param block                             $block
	 * @param                                   $comments_table
	 * @param                                   $images_table
	 * @internal param image\image $image
	 * @internal param album\album $album
	 */

	public function __construct(\phpbb\user $user, \phpbb\db\driver\driver_interface $db,
								\phpbbgallery\core\config $config, \phpbbgallery\core\auth\auth $auth, \phpbbgallery\core\block $block,
								$comments_table, $images_table)
	{
		$this->user = $user;
		$this->db = $db;
		$this->config = $config;
		$this->auth = $auth;
		$this->block = $block;
		$this->comments_table = $comments_table;
		$this->images_table = $images_table;
	}

	/**
	 * Is the user allowed to comment?
	 * Following statements must be true:
	 *    - User must have permissions.
	 *    - User is neither owner of the image nor guest.
	 *    - Album and image are not locked.
	 *
	 * @param $album_data
	 * @param $image_data
	 * @return bool
	 */
	public function is_allowed($album_data, $image_data)
	{
		return $this->config->get('allow_comments') && (!$this->config->get('comment_user_control') || $image_data['image_allow_comments']) &&
			($this->auth->acl_check('m_status', $album_data['album_id'], $album_data['album_user_id']) ||
			(($image_data['image_status'] == $this->block->get_image_status_approved()) && ($album_data['album_status'] != $this->block->get_album_status_locked())));
	}

	/**
	 * Is the user able to comment?
	 * Following statements must be true:
	 *    - User must be allowed to rate
	 *    - If the image is in a contest, it must be finished
	 *
	 * @param $album_data
	 * @param $image_data
	 * @return bool
	 */
	public function is_able($album_data, $image_data)
	{
		return $this->is_allowed($album_data, $image_data); //&& phpbb_ext_gallery_core_contest::is_step('comment', $album_data);
	}

	/**
	 * Add a comment
	 *
	 * @param        $data
	 * @param string $comment_username
	 * @return int|void
	 */
	public function add($data, $comment_username = '')
	{
		if (!isset($data['comment_image_id']) || !isset($data['comment']))
		{
			return;
		}

		$data = $data + array(
			'comment_user_id'		=> $this->user->data['user_id'],
			'comment_username'		=> ($this->user->data['user_id'] != ANONYMOUS) ? $this->user->data['username'] : $comment_username,
			'comment_user_colour'	=> $this->user->data['user_colour'],
			'comment_user_ip'		=> $this->user->ip,
			'comment_time'			=> time(),
		);

		$this->db->sql_query('INSERT INTO ' .$this->comments_table .' ' . $this->db->sql_build_array('INSERT', $data));
		$newest_comment_id = (int) $this->db->sql_nextid();
		$this->config->inc('num_comments', 1);

		$sql = 'UPDATE ' . $this->images_table . ' 
			SET image_comments = image_comments + 1,
				image_last_comment = ' . (int) $newest_comment_id . '
			WHERE image_id = ' . (int) $data['comment_image_id'];
		$this->db->sql_query($sql);

		return $newest_comment_id;
	}

	/**
	 * Edit comment
	 * @param $comment_id
	 * @param $data
	 * @return bool|void
	 */
	public function edit($comment_id, $data)
	{
		if (!isset($data['comment']))
		{
			return;
		}

		$data = $data + array(
			'comment_edit_time'		=> time(),
			'comment_edit_user_id'	=> $this->user->data['user_id'],
		);

		$sql = 'UPDATE ' . $this->comments_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE comment_id = ' . (int) $comment_id;
		$this->db->sql_query($sql);

		return true;
	}

	/**
	 * Sync last comment information
	 * @param bool $image_ids
	 */
	public function sync_image_comments($image_ids = false)
	{
		$sql_where = $sql_where_image = '';
		$resync = array();
		if ($image_ids != false)
		{
			$image_ids = self::cast_mixed_int2array($image_ids);
			$sql_where = 'WHERE ' . $this->db->sql_in_set('comment_image_id', $image_ids);
			$sql_where_image = 'WHERE ' . $this->db->sql_in_set('image_id', $image_ids);
		}

		$sql = 'SELECT comment_image_id, COUNT(comment_id) AS num_comments, MAX(comment_id) AS last_comment
			FROM ' . $this->comments_table . ' 
			' . $sql_where . '
			GROUP BY comment_image_id, comment_id
			ORDER BY comment_id DESC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$resync[$row['comment_image_id']] = array(
				'last_comment'	=> $row['last_comment'],
				'num_comments'	=> $row['num_comments'],
			);
		}
		$this->db->sql_freeresult($result);

		$sql = 'UPDATE ' . $this->images_table . ' 
			SET image_last_comment = 0,
				image_comments = 0
			' . $sql_where_image;
		$this->db->sql_query($sql);

		if (!empty($resync))
		{
			foreach ($resync as $image_id => $data)
			{
				$sql = 'UPDATE ' . $this->images_table . ' 
					SET image_last_comment = ' . (int) $data['last_comment'] . ',
						image_comments = ' . (int) $data['num_comments'] . '
					WHERE image_id = ' . (int) $image_id;
				$this->db->sql_query($sql);
			}
		}
	}

	/**
	* Delete comments
	*
	* @param	mixed	$comment_ids	Array or integer with comment_id we delete.
	*/
	public function delete_comments($comment_ids)
	{
		$comment_ids = $this->cast_mixed_int2array($comment_ids);

		$sql = 'SELECT comment_image_id, COUNT(comment_id) AS num_comments
			FROM ' . $this->comments_table . '
			WHERE ' . $this->db->sql_in_set('comment_id', $comment_ids) . '
			GROUP BY comment_image_id';
		$result = $this->db->sql_query($sql);

		$image_ids = array();
		$total_comments = 0;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$image_ids[] = (int) $row['comment_image_id'];
			$total_comments += $row['num_comments'];
		}
		$this->db->sql_freeresult($result);

		$sql = 'DELETE FROM ' . $this->comments_table . '
			WHERE ' . $this->db->sql_in_set('comment_id', $comment_ids);
		$this->db->sql_query($sql);

		$this->sync_image_comments($image_ids);

		$this->config->dec('num_comments', $total_comments);
	}

	/**
	* Delete comments for given image_ids
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete the comments.
	* @param	bool	$reset_stats	Shall we also reset the statistics? We can save that query, when the images are deleted anyway.
	*/
	public function delete_images($image_ids, $reset_stats = false)
	{
		$image_ids = $this->cast_mixed_int2array($image_ids);

		$sql = 'DELETE FROM ' . $this->comments_table . '
			WHERE ' . $this->db->sql_in_set('comment_image_id', $image_ids);
		$this->db->sql_query($sql);

		if ($reset_stats)
		{
			$sql = 'UPDATE ' . $this->images_table . '
				SET image_comments = 0
					image_last_comment = 0
				WHERE ' . $this->db->sql_in_set('image_id', $image_ids);
			$this->db->sql_query($sql);
		}
	}

	public function cast_mixed_int2array($ids)
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
