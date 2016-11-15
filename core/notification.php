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
	 * notification constructor.
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\user $user
	 * @param $watch_table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user,
								$watch_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->watch_table = $watch_table;
	}

	/**
	 * Add images to watch-list
	 *
	 * @param    mixed 		$image_ids Array or integer with image_id where we delete from the watch-list.
	 * @param 	bool|int 	$user_id   If not set, it uses the currents user_id
	 */
	public function add($image_ids, $user_id = false)
	{
		$image_ids = self::cast_mixed_int2array($image_ids);
		$user_id = (int) (($user_id) ? $user_id : $this->user->data['user_id']);

		// First check if we are not subscribed alredy for some
		$sql = 'SELECT * FROM ' . $this->watch_table . '  WHERE user_id = ' . (int) $user_id . ' AND ' . $this->db->sql_in_set('image_id', $image_ids);
		$result = $this->db->sql_query($sql);
		$exclude = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$exclude[] = (int) $row['image_id'];
		}
		$image_ids = array_diff($image_ids, $exclude);

		foreach ($image_ids as $image_id)
		{
			$sql_ary = array(
				'image_id'		=> (int) $image_id,
				'user_id'		=> (int) $user_id,
			);
			$sql = 'INSERT INTO ' . $this->watch_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Add albums to watch-list
	 *
	 * @param    mixed 		$album_ids Array or integer with album_id where we delete from the watch-list.
	 * @param 	bool|int 	$user_id   If not set, it uses the currents user_id
	 */
	public function add_albums($album_ids, $user_id = false)
	{
		$album_ids = self::cast_mixed_int2array($album_ids);
		$user_id = (int) (($user_id) ? $user_id : $this->user->data['user_id']);

		foreach ($album_ids as $album_id)
		{
			$sql_ary = array(
				'album_id'		=> (int) $album_id,
				'user_id'		=> (int) $user_id,
			);
			$sql = 'INSERT INTO ' . $this->watch_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
			$this->db->sql_query($sql);
		}
	}

	/**
	* Remove images from watch-list
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete from the watch-list.
	* @param	mixed	$user_ids		If not set, it uses the currents user_id
	*/
	public function remove($image_ids, $user_ids = false)
	{
		$image_ids = self::cast_mixed_int2array($image_ids);
		$user_ids = self::cast_mixed_int2array((($user_ids) ? $user_ids : $this->user->data['user_id']));

		$sql = 'DELETE FROM ' . $this->watch_table . ' 
			WHERE ' . $this->db->sql_in_set('user_id', $user_ids) . '
				AND ' . $this->db->sql_in_set('image_id', $image_ids);
		$this->db->sql_query($sql);
	}

	/**
	* Remove albums from watch-list
	*
	* @param	mixed	$album_ids		Array or integer with album_id where we delete from the watch-list.
	* @param	mixed	$user_ids		If not set, it uses the currents user_id
	*/
	public function remove_albums($album_ids, $user_ids = false)
	{
		$album_ids = self::cast_mixed_int2array($album_ids);
		$user_ids = self::cast_mixed_int2array((($user_ids) ? $user_ids : $this->user->data['user_id']));

		$sql = 'DELETE FROM ' . $this->watch_table . ' 
			WHERE ' . $this->db->sql_in_set('user_id', $user_ids) . '
				AND ' . $this->db->sql_in_set('album_id', $album_ids);
		$this->db->sql_query($sql);
	}

	/**
	* Delete given image_ids from watch-list
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete from watch-list.
	*/
	public function delete_images($image_ids)
	{
		$image_ids = self::cast_mixed_int2array($image_ids);

		$sql = 'DELETE FROM ' . $this->watch_table . ' 
			WHERE ' . $this->db->sql_in_set('image_id', $image_ids);
		$this->db->sql_query($sql);
	}


	/**
	* Delete given album_ids from watch-list
	*
	* @param	mixed	$album_ids		Array or integer with album_id where we delete from watch-list.
	*/
	public function delete_albums($album_ids)
	{
		$album_ids = self::cast_mixed_int2array($album_ids);

		$sql = 'DELETE FROM ' . $this->watch_table . ' 
			WHERE ' . $this->db->sql_in_set('album_id', $album_ids);

		$this->db->sql_query($sql);
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
