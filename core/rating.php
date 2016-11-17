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

class rating
{
	/**
	* The image ID we want to rate
	*/
	public $image_id = 0;

	/**
	* Private objects with the values for the image/album from the database
	*/
	private $image_data = null;
	private $album_data = null;

	/**
	* Rating the user gave the image.
	*/
	public $user_rating = null;

	/**
	* Is rating currently possible?
	* Might be blocked because of contest-settings.
	*/
	public $rating_enabled = false;

	/**
	* Classic-rating box with a dropdown.
	*/
	const MODE_SELECT = 1;

	/**
	* Rating with stars, like the old-system from youtube.
	//@todo: const MODE_STARS = 2;
	*/

	/**
	* Simple thumbs up or down.
	//@todo: const MODE_THUMB = 3;
	*/

	/**
	 * Constructor
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\template\template $template
	 * @param \phpbb\user $user
	 * @param \phpbb\request\request $request
	 * @param config $gallery_config
	 * @param auth\auth $gallery_auth
	 * @param $images_table
	 * @param $albums_table
	 * @param $rates_table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, \phpbb\request\request $request,
	\phpbbgallery\core\config $gallery_config, \phpbbgallery\core\auth\auth $gallery_auth,
	$images_table, $albums_table, $rates_table)
	{
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->request = $request;
		$this->gallery_config = $gallery_config;
		$this->gallery_auth = $gallery_auth;
		$this->images_table = $images_table;
		$this->albums_table = $albums_table;
		$this->rates_table = $rates_table;
	}

	/**
	 * Load data for the class to work with
	 *
	 * @param    int $image_id
	 * @param array|bool $image_data Array with values from the image-table of the image
	 * @param array|bool $album_data Array with values from the album-table of the image's album
	 */
	public function loader($image_id, $image_data = false, $album_data = false)
	{
		$this->image_id = (int) $image_id;
		if ($image_data)
		{
			$this->image_data = $image_data;
		}
		if ($album_data)
		{
			$this->album_data = $album_data;
		}
	}

	/**
	 * Returns the value of image_data key.
	 * If the value is missing, it is queried from the database.
	 * @param $key
	 * @return
	 */
	private function image_data($key)
	{
		if ($this->image_data == null)
		{
			$sql = 'SELECT *
				FROM ' . $this->images_table . '
				WHERE image_id = ' . (int) $this->image_id;
			$result = $this->db->sql_query($sql);
			$this->image_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($this->image_data == false)
			{
				trigger_error('IMAGE_NOT_EXIST');
			}
		}

		return $this->image_data[$key];
	}

	/**
	 * Returns the value of album_data key.
	 * If the value is missing, it is queried from the database.
	 *
	 * @param    $key    string    The value of the album data, if true it returns the hole array.
	 * @return mixed|null
	 */
	private function album_data($key)
	{
		if ($this->album_data == null)
		{
			$sql = 'SELECT *
				FROM ' . $this->albums_table . '
				WHERE album_id = ' . (int) $this->image_data('album_id');
			$result = $this->db->sql_query($sql);
			$this->album_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($this->album_data == false)
			{
				trigger_error('ALBUM_NOT_EXIST');
			}
		}

		return ($key === true) ? $this->album_data : $this->album_data[$key];
	}

	/**
	* Displays the box where the user can rate the image.
	*/
	public function display_box()
	{
		$this->template->assign_var('GALLERY_RATING', self::MODE_SELECT);//@todo: phpbb_ext_gallery_core_config::get('rating_mode'));

		switch (self::MODE_SELECT)//@todo: phpbb_ext_gallery_core_config::get('rating_mode'))
		{
			//@todo: self::MODE_THUMB:
			//@todo: self::MODE_STARS:
			case self::MODE_SELECT:
			default:
				// @TODO We do not have contests for now
				/*if ($this->album_data('contest_id'))
				{
					if (time() < ($this->album_data('contest_start') + $this->album_data('contest_rating')))
					{
						$template->assign_var('GALLERY_NO_RATING_MESSAGE', $user->lang('CONTEST_RATING_STARTS', $user->format_date(($this->album_data('contest_start') + $this->album_data('contest_rating')), false, true)));
						return;
					}
					if (($this->album_data('contest_start') + $this->album_data('contest_end')) < time())
					{
						$template->assign_var('GALLERY_NO_RATING_MESSAGE', $user->lang('CONTEST_RATING_ENDED', $user->format_date(($this->album_data('contest_start') + $this->album_data('contest_end')), false, true)));
						return;
					}
				}*/
				for ($i = 1; $i <= $this->gallery_config->get('max_rating'); $i++)
				{
					$this->template->assign_block_vars('rate_scale', array(
						'RATE_POINT'	=> $i,
					));
				}
			break;
		}

		$this->rating_enabled = true;
	}

	/**
	 * Get rating for a image
	 *
	 * @param bool|Personal $user_rating Personal rating of the user is displayed in most cases.
	 * @param bool|Shall $display_contest_end Shall we display the end-time of the contest? This requires the album-data to be filled.
	 * @return string Returns a string containing the information how the image was rated in average and how often.
	 */
	public function get_image_rating($user_rating = false, $display_contest_end = true)
	{
		$this->template->assign_var('GALLERY_RATING', self::MODE_SELECT);//@todo: phpbb_ext_gallery_core_config::get('rating_mode'));

		switch (self::MODE_SELECT)//@todo: phpbb_ext_gallery_core_config::get('rating_mode'))
		{
			//@todo: self::MODE_THUMB:
			//@todo: self::MODE_STARS:
			case self::MODE_SELECT:
			default:
				if ($this->image_data('image_contest'))
				{
					if (!$display_contest_end)
					{
						return $this->user->lang['CONTEST_RATING_HIDDEN'];
					}
					return $this->user->lang('CONTEST_RESULT_HIDDEN', $this->user->format_date(($this->album_data('contest_start') + $this->album_data('contest_end')), false, true));
				}
				else
				{
					if ($user_rating)
					{
						return $this->user->lang('RATING_STRINGS_USER', (int) $this->image_data('image_rates'), $this->get_image_rating_value(), $user_rating);
					}
					return $this->user->lang('RATING_STRINGS', (int) $this->image_data('image_rates'), $this->get_image_rating_value());
				}
			break;
		}
	}

	/**
	* Get rated value for a image
	*/
	private function get_image_rating_value()
	{
		/*if (phpbb_ext_gallery_core_contest::$mode == phpbb_ext_gallery_core_contest::MODE_SUM)
		{
			return $this->image_data('image_rate_points');
		}
		else
		{*/
			return ($this->image_data('image_rate_avg') / 100);
		//}
	}

	/**
	* Is the user allowed to rate?
	* Following statements must be true:
	*	- User must have permissions.
	*	- User is neither owner of the image nor guest.
	*	- Album and image are not locked.
	*
	* @return	bool
	*/
	public function is_allowed()
	{
		return $this->gallery_auth->acl_check('i_rate', $this->album_data('album_id'), $this->album_data('album_user_id')) &&
			($this->user->data['user_id'] != $this->image_data('image_user_id')) && ($this->user->data['user_id'] != ANONYMOUS) &&
			($this->album_data('album_status') != \phpbbgallery\core\block::ALBUM_LOCKED) && ($this->image_data('image_status') == \phpbbgallery\core\block::STATUS_APPROVED);
	}

	/**
	* Is the user able to rate?
	* Following statements must be true:
	*	- User must be allowed to rate
	*	- If the image is in a contest, it must be in the rating timespan
	*
	* @return	bool
	*/
	public function is_able()
	{
		return $this->is_allowed(); //&& phpbb_ext_gallery_core_contest::is_step('rate', $this->album_data(true));
	}

	/**
	* Get rating from a user for a given image
	*
	* @param	int		$user_id
	*
	* @return	mixed	False if the user did not rate or is guest, otherwise int the points.
	*/
	public function get_user_rating($user_id)
	{
		$user_id = (int) $user_id;
		if (isset($this->user_rating[$user_id]))
		{
			return $this->user_rating[$user_id];
		}
		if ($user_id == ANONYMOUS)
		{
			return false;
		}

		$sql = 'SELECT rate_point
			FROM ' . $this->rates_table . '
			WHERE rate_image_id = ' . (int) $this->image_id . '
				AND rate_user_id = ' . $user_id;
		$result = $this->db->sql_query($sql);
		$rating = $this->db->sql_fetchfield('rate_point');
		$this->db->sql_freeresult($result);

		$this->user_rating[$user_id] = (is_bool($rating)) ? $rating : (int) $rating;
		return $this->user_rating[$user_id];
	}

	/**
	 * Submit rating for an image.
	 *
	 * @param bool|int $user_id
	 * @param bool|int $points
	 * @param bool|string $user_ip Can be empty, function falls back to $user->ip
	 * @return bool
	 */
	public function submit_rating($user_id = false, $points = false, $user_ip = false)
	{
		switch (self::MODE_SELECT)//@todo: phpbb_ext_gallery_core_config::get('rating_mode'))
		{
			//@todo: self::MODE_THUMB:
			//@todo: self::MODE_STARS:
			case self::MODE_SELECT:
			default:
				$user_id = ($user_id) ? $user_id : $this->user->data['user_id'];
				$points = ($points) ? $points : $this->request->variable('rating', 0);
				$points = max(1, min($points, $this->gallery_config->get('max_rating')));
			break;
		}

		if (($user_id == ANONYMOUS) || $this->get_user_rating($user_id))
		{
			return false;
		}

		$this->insert_rating($user_id, $points, $user_ip);

		$this->recalc_image_rating($this->image_id);
		$this->user_rating[$user_id] = $points;
	}

	/**
	 * Insert the rating into the database.
	 *
	 * @param    int $user_id
	 * @param    int $points
	 * @param bool|string $user_ip Can be empty, function falls back to $user->ip
	 */
	private function insert_rating($user_id, $points, $user_ip = false)
	{
		$sql_ary = array(
			'rate_image_id'	=> $this->image_id,
			'rate_user_id'	=> $user_id,
			'rate_user_ip'	=> ($user_ip) ? $user_ip : $this->user->ip,
			'rate_point'	=> $points,
		);
		$this->db->sql_query('INSERT INTO ' . $this->rates_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
	}

	/**
	* Recalculate the average image-rating and such stuff.
	*
	* @param	mixed	$image_ids	Array or integer with image_id where we recalculate the rating.
	*/
	public function recalc_image_rating($image_ids)
	{
		if (is_array($image_ids))
		{
			$image_ids = array_map('intval', $image_ids);
		}
		else
		{
			$image_ids = (int) $image_ids;
		}

		$sql = 'SELECT rate_image_id, COUNT(rate_user_ip) image_rates, AVG(rate_point) image_rate_avg, SUM(rate_point) image_rate_points
			FROM ' . $this->rates_table . '
			WHERE ' . $this->db->sql_in_set('rate_image_id', $image_ids, false, true) . '
			GROUP BY rate_image_id';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$sql = 'UPDATE ' . $this->images_table . '
				SET image_rates = ' . $row['image_rates'] . ',
					image_rate_points = ' . $row['image_rate_points'] . ',
					image_rate_avg = ' . round($row['image_rate_avg'], 2) * 100 . '
				WHERE image_id = ' . $row['rate_image_id'];
			$this->db->sql_query($sql);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Delete all ratings for given image_ids
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete the rating.
	* @param	bool	$reset_average	Shall we also reset the average? We can save that query, when the images are deleted anyway.
	*/
	public function delete_ratings($image_ids, $reset_average = false)
	{
		if (is_array($image_ids))
		{
			$image_ids = array_map('intval', $image_ids);
		}
		else
		{
			$image_ids = (int) $image_ids;
		}

		$sql = 'DELETE FROM ' . $this->rates_table . '
			WHERE ' . $this->db->sql_in_set('rate_image_id', $image_ids, false, true);
		$result = $this->db->sql_query($sql);

		if ($reset_average)
		{
			$sql = 'UPDATE ' . $this->images_table . '
				SET image_rates = 0,
					image_rate_points = 0,
					image_rate_avg = 0
				WHERE ' . $this->db->sql_in_set('image_id', $image_ids);
			$this->db->sql_query($sql);
		}
	}
}
