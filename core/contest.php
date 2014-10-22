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

class phpbb_ext_gallery_core_contest
{
	const NUM_IMAGES = 3;

	/**
	* There are different modes to calculate who won the contest.
	* This value should be one of the constant-names below.
	*/
	static public $mode = self::MODE_AVERAGE;

	/**
	* The image with the highest average wins.
	*/
	const MODE_AVERAGE = 1;
	/**
	* The image with the highest number of total points wins.
	*/
	const MODE_SUM = 2;

	/**
	* Get the contest row from the table
	*
	* @param	int		$id				ID of the contest or album, depending on second parameter
	* @param	string	$mode			contest or album ID to get the contest.
	* @param	bool	$throw_error	Shall we throw an error if the contest was not found?
	*
	* @return	mixed	Either the array or boolean false if contest does not exist
	*/
	function get_contest($id, $mode = 'contest', $throw_error = true)
	{
		global $db;

		$sql = 'SELECT *
			FROM ' . GALLERY_CONTESTS_TABLE . '
			WHERE ' . (($mode = 'album') ? 'contest_album_id' : 'contest_id') . ' = ' . (int) $id;
		$result = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$row && $throw_error)
		{
			trigger_error('NO_CONTEST', E_USER_ERROR);
		}

		return (!$row) ? false : $row;
	}

	static private function get_tabulation()
	{
		switch (self::$mode)
		{
			case self::MODE_AVERAGE:
				return 'image_rate_avg DESC, image_rate_points DESC, image_id ASC';
			case self::MODE_SUM:
				return 'image_rate_points DESC, image_rate_avg DESC, image_id ASC';
		}
	}

	static public function is_step($mode, $album_data)
	{
		switch ($mode)
		{
			case 'upload':
				return (!$album_data['contest_id'] || ((($album_data['contest_start']) < time()) &&
					 (time() < ($album_data['contest_start'] + $album_data['contest_rating']))));
			case 'rate':
				return (!$album_data['contest_id'] || ((($album_data['contest_start'] + $album_data['contest_rating']) < time()) &&
					 (time() < ($album_data['contest_start'] + $album_data['contest_end']))));
			case 'comment':
				return (!$album_data['contest_id'] || (time() > ($album_data['contest_start'] + $album_data['contest_end'])));
		}
	}

	static public function end($album_id, $contest_id, $end_time)
	{
		global $db;

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_contest = ' . phpbb_gallery_image::NO_CONTEST . '
			WHERE image_album_id = ' . $album_id;
		$db->sql_query($sql);

		$sql = 'SELECT image_id
			FROM ' . GALLERY_IMAGES_TABLE . '
			WHERE image_album_id = ' . $album_id . '
			ORDER BY ' . self::get_tabulation();
		$result = $db->sql_query_limit($sql, self::NUM_IMAGES);
		$first = (int) $db->sql_fetchfield('image_id');
		$second = (int) $db->sql_fetchfield('image_id');
		$third = (int) $db->sql_fetchfield('image_id');
		$db->sql_freeresult($result);

		$sql = 'UPDATE ' . GALLERY_CONTESTS_TABLE . '
			SET contest_marked = ' . phpbb_gallery_image::NO_CONTEST . ",
				contest_first = $first,
				contest_second = $second,
				contest_third = $third
			WHERE contest_id = " . (int) $contest_id;
		$db->sql_query($sql);

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_contest_end = ' . (int) $end_time . ',
				image_contest_rank = 1
			WHERE image_id = ' . $first;
		$db->sql_query($sql);

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_contest_end = ' . (int) $end_time . ',
				image_contest_rank = 2
			WHERE image_id = ' . $second;
		$db->sql_query($sql);

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_contest_end = ' . (int) $end_time . ',
				image_contest_rank = 3
			WHERE image_id = ' . $third;
		$db->sql_query($sql);

		phpbb_gallery_config::inc('contests_ended', 1);
	}

	static public function resync_albums($album_ids)
	{
		if (is_array($album_ids))
		{
			$album_ids = array_map('intval', $album_ids);
			foreach ($album_ids as $album_id)
			{
				self::resync($album_id);
			}
		}
		else
		{
			self::resync((int) $album_ids);
		}
	}

	static public function resync($album_id)
	{
		global $db;

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_contest = ' . phpbb_gallery_image::NO_CONTEST . '
			WHERE image_album_id = ' . $album_id;
		$db->sql_query($sql);

		$sql = 'SELECT image_id
			FROM ' . GALLERY_IMAGES_TABLE . '
			WHERE image_album_id = ' . $album_id . '
			ORDER BY ' . self::get_tabulation();
		$result = $db->sql_query_limit($sql, self::NUM_IMAGES);
		$first = (int) $db->sql_fetchfield('image_id');
		$second = (int) $db->sql_fetchfield('image_id');
		$third = (int) $db->sql_fetchfield('image_id');
		$db->sql_freeresult($result);

		$sql = 'UPDATE ' . GALLERY_CONTESTS_TABLE . "
			SET contest_first = $first,
				contest_second = $second,
				contest_third = $third
			WHERE contest_album_id = " . (int) $album_id;
		$db->sql_query($sql);

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_contest_rank = 1
			WHERE image_id = ' . $first;
		$db->sql_query($sql);

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_contest_rank = 2
			WHERE image_id = ' . $second;
		$db->sql_query($sql);

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
			SET image_contest_rank = 3
			WHERE image_id = ' . $third;
		$db->sql_query($sql);
	}
}
