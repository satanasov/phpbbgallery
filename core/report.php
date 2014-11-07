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

class report
{
	const UNREPORTED = 0;
	const OPEN = 1;
	const LOCKED = 2;

	/**
	* Report an image
	*/
	static public function add($data)
	{
		global $db, $user, $table_prefix;

		if (!isset($data['report_album_id']) || !isset($data['report_image_id']) || !isset($data['report_note']))
		{
			return;
		}
		$data = $data + array(
			'reporter_id'				=> $user->data['user_id'],
			'report_time'				=> time(),
			'report_status'				=> self::OPEN,
		);
		$sql = 'INSERT INTO ' . $table_prefix . 'gallery_reports ' . $db->sql_build_array('INSERT', $data);
		$db->sql_query($sql);

		$report_id = (int) $db->sql_nextid();

		$sql = 'UPDATE ' . $table_prefix . 'gallery_images 
			SET image_reported = ' . $report_id . '
			WHERE image_id = ' . (int) $data['report_image_id'];
		$db->sql_query($sql);
	}

	/**
	* Change status of a report
	*
	* @param	mixed	$report_ids		Array or integer with report_id.
	* @param	int		$user_id		If not set, it uses the currents user_id
	*/
	static public function change_status($new_status, $report_ids, $user_id = false)
	{
		global $db, $user, $table_prefix;

		$sql_ary = array(
			'report_manager'		=> (int) (($user_id) ? $user_id : $user->data['user_id']),
			'report_status'			=> $new_status,
		);
		$report_ids = self::cast_mixed_int2array($report_ids);

		$sql = 'UPDATE ' . $table_prefix . 'gallery_reports SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE ' . $db->sql_in_set('report_id', $report_ids);
		$db->sql_query($sql);

		if ($new_status == self::LOCKED)
		{
			$sql = 'UPDATE ' . $table_prefix . 'gallery_images
				SET image_reported = ' . self::UNREPORTED . '
				WHERE ' . $db->sql_in_set('image_reported', $report_ids);
			$db->sql_query($sql);
		}
		else
		{
			$sql = 'SELECT report_image_id, report_id
				FROM ' . $table_prefix . 'gallery_reports
				WHERE report_status = ' . self::OPEN . '
					AND ' . $db->sql_in_set('report_id', $report_ids);
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$sql = 'UPDATE ' . $table_prefix . 'gallery_images
					SET image_reported = ' . (int) $row['report_id'] . '
					WHERE image_id = ' . (int) $row['report_image_id'];
				$db->sql_query($sql);
			}
			$db->sql_freeresult($result);
		}
	}

	/**
	* Move an image from one album to another
	*
	* @param	mixed	$image_ids		Array or integer with image_id.
	*/
	static public function move_images($image_ids, $move_to)
	{
		global $db, $table_prefix;

		$image_ids = self::cast_mixed_int2array($image_ids);

		$sql = 'UPDATE ' . $table_prefix . 'gallery_reports
			SET report_album_id = ' . (int) $move_to . '
			WHERE ' . $db->sql_in_set('report_image_id', $image_ids);
		$db->sql_query($sql);
	}

	/**
	* Move the content from one album to another
	*
	* @param	mixed	$image_ids		Array or integer with image_id.
	*/
	static public function move_album_content($move_from, $move_to)
	{
		global $db, $table_prefix;

		$sql = 'UPDATE ' . $table_prefix . 'gallery_reports
			SET report_album_id = ' . (int) $move_to . '
			WHERE report_album_id = ' . (int) $move_from;
		$db->sql_query($sql);
	}

	/**
	* Delete reports for given report_ids
	*
	* @param	mixed	$report_ids		Array or integer with report_id.
	*/
	static public function delete($report_ids)
	{
		global $db, $table_prefix;

		$report_ids = self::cast_mixed_int2array($report_ids);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_reports
			WHERE ' . $db->sql_in_set('report_id', $report_ids);
		$result = $db->sql_query($sql);

		$sql = 'UPDATE ' . $table_prefix . 'gallery_images
			SET image_reported = ' . self::UNREPORTED . '
			WHERE ' . $db->sql_in_set('image_reported', $report_ids);
		$db->sql_query($sql);
	}


	/**
	* Delete reports for given image_ids
	*
	* @param	mixed	$image_ids		Array or integer with image_id.
	*/
	static public function delete_images($image_ids)
	{
		global $db, $table_prefix;

		$image_ids = self::cast_mixed_int2array($image_ids);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_reports
			WHERE ' . $db->sql_in_set('report_image_id', $image_ids);
		$result = $db->sql_query($sql);
	}


	/**
	* Delete reports for given album_ids
	*
	* @param	mixed	$album_ids		Array or integer with album_id.
	*/
	static public function delete_albums($album_ids)
	{
		global $db, $table_prefix;

		$album_ids = self::cast_mixed_int2array($album_ids);

		$sql = 'DELETE FROM ' . $table_prefix . 'gallery_reports
			WHERE ' . $db->sql_in_set('report_album_id', $album_ids);
		$result = $db->sql_query($sql);
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
