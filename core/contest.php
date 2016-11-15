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

class contest
{
	/**
	 * I will have to see where is contest used except here and make it work
	 * but for the time being - redefine contest constants here as private
	 */
	/**
	 * Variables regarding the image contest relation
	 */
	private $NO_CONTEST = 0;

	/**
	 * The image is element of an open contest. Only moderators can see the user_name of the user.
	 */
	private $IN_CONTEST = 1;


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

	public function __construct(\phpbb\db\driver\driver_interface $db,
								\phpbbgallery\core\config $gallery_config,
								$images_table, $contests_table)
	{
		$this->db = $db;
		$this->gallery_config = $gallery_config;
		$this->images_table = $images_table;
		$this->contest_table = $contests_table;
	}

	/**
	* Get the contest row from the table
	*
	* @param	int		$id				ID of the contest or album, depending on second parameter
	* @param	string	$mode			contest or album ID to get the contest.
	* @param	bool	$throw_error	Shall we throw an error if the contest was not found?
	*
	* @return	mixed	Either the array or boolean false if contest does not exist
	*/
	public function get_contest($id, $mode = 'contest', $throw_error = true)
	{
		$sql = 'SELECT *
			FROM ' . $this->contest_table . '
			WHERE ' . (($mode = 'album') ? 'contest_album_id' : 'contest_id') . ' = ' . (int) $id;
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row && $throw_error)
		{
			trigger_error('NO_CONTEST', E_USER_ERROR);
		}

		return (!$row) ? false : $row;
	}

	private function get_tabulation()
	{
		switch (self::$mode)
		{
			case self::MODE_AVERAGE:
				return 'image_rate_avg DESC, image_rate_points DESC, image_id ASC';
			case self::MODE_SUM:
				return 'image_rate_points DESC, image_rate_avg DESC, image_id ASC';
		}
	}

	public function is_step($mode, $album_data)
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

	public function end($album_id, $contest_id, $end_time)
	{
		$sql = 'UPDATE ' . $this->images_table . '
			SET image_contest = ' . $this->NO_CONTEST . '
			WHERE image_album_id = ' . (int) $album_id;
		$this->db->sql_query($sql);

		$sql = 'SELECT image_id
			FROM ' . $this->images_table . '
			WHERE image_album_id = ' . (int) $album_id . '
			ORDER BY ' .$this->get_tabulation();
		$result = $this->db->sql_query_limit($sql, self::NUM_IMAGES);
		$first = (int) $this->db->sql_fetchfield('image_id');
		$second = (int) $this->db->sql_fetchfield('image_id');
		$third = (int) $this->db->sql_fetchfield('image_id');
		$this->db->sql_freeresult($result);

		$sql = 'UPDATE ' . $this->contest_table . '
			SET contest_marked = ' . $this->NO_CONTEST . ",
				contest_first = $first,
				contest_second = $second,
				contest_third = $third
			WHERE contest_id = " . (int) $contest_id;
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . $this->images_table . '
			SET image_contest_end = ' . (int) $end_time . ',
				image_contest_rank = 1
			WHERE image_id = ' . $first;
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . $this->images_table . '
			SET image_contest_end = ' . (int) $end_time . ',
				image_contest_rank = 2
			WHERE image_id = ' . $second;
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . $this->images_table . '
			SET image_contest_end = ' . (int) $end_time . ',
				image_contest_rank = 3
			WHERE image_id = ' . $third;
		$this->db->sql_query($sql);

		$this->gallery_config->inc('contests_ended', 1);
	}

	public function resync_albums($album_ids)
	{
		if (is_array($album_ids))
		{
			$album_ids = array_map('intval', $album_ids);
			foreach ($album_ids as $album_id)
			{
				$this->resync($album_id);
			}
		}
		else
		{
			$this->resync((int) $album_ids);
		}
	}

	public function resync($album_id)
	{
		$sql = 'UPDATE ' . $this->images_table . '
			SET image_contest = ' . $this->NO_CONTEST . '
			WHERE image_album_id = ' . (int) $album_id;
		$this->db->sql_query($sql);

		$sql = 'SELECT image_id
			FROM ' . $this->images_table . '
			WHERE image_album_id = ' . (int) $album_id . '
			ORDER BY ' . $this->get_tabulation();
		$result = $this->db->sql_query_limit($sql, self::NUM_IMAGES);
		$first = (int) $this->db->sql_fetchfield('image_id');
		$second = (int) $this->db->sql_fetchfield('image_id');
		$third = (int) $this->db->sql_fetchfield('image_id');
		$this->db->sql_freeresult($result);

		$sql = 'UPDATE ' . $this->contest_table . "
			SET contest_first = $first,
				contest_second = $second,
				contest_third = $third
			WHERE contest_album_id = " . (int) $album_id;
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . $this->images_table . '
			SET image_contest_rank = 1
			WHERE image_id = ' . $first;
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . $this->images_table . '
			SET image_contest_rank = 2
			WHERE image_id = ' . $second;
		$this->db->sql_query($sql);

		$sql = 'UPDATE ' . $this->images_table . '
			SET image_contest_rank = 3
			WHERE image_id = ' . $third;
		$this->db->sql_query($sql);
	}
}
