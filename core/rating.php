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

class phpbb_ext_gallery_core_rating
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
	*
	* @param	int		$image_id
	* @param	array	$image_data		Array with values from the image-table of the image
	* @param	array	$album_data		Array with values from the album-table of the image's album
	*/
	public function __construct($image_id, $image_data = false, $album_data = false)
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
	*/
	private function image_data($key)
	{
		if ($this->image_data == null)
		{
			global $db;

			$sql = 'SELECT *
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE image_id = ' . $this->image_id;
			$result = $db->sql_query($sql);
			$this->image_data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

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
	* @param	$key	string	The value of the album data, if true it returns the hole array.
	*/
	private function album_data($key)
	{
		if ($this->album_data == null)
		{
			global $db;

			$sql = 'SELECT *
				FROM ' . GALLERY_ALBUMS_TABLE . '
				WHERE album_id = ' . (int) $this->image_data('album_id');
			$result = $db->sql_query($sql);
			$this->album_data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

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
		global $template, $user;

		$template->assign_var('GALLERY_RATING', self::MODE_SELECT);//@todo: phpbb_ext_gallery_core_config::get('rating_mode'));

		switch (self::MODE_SELECT)//@todo: phpbb_ext_gallery_core_config::get('rating_mode'))
		{
			//@todo: self::MODE_THUMB:
			//@todo: self::MODE_STARS:
			case self::MODE_SELECT:
			default:
				if ($this->album_data('contest_id'))
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
				}
				for ($i = 1; $i <= phpbb_ext_gallery_core_config::get('max_rating'); $i++)
				{
					$template->assign_block_vars('rate_scale', array(
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
	* @param	$user_rating			Personal rating of the user is displayed in most cases.
	* @param	$display_contest_end	Shall we display the end-time of the contest? This requires the album-data to be filled.
	* @return	string					Returns a string containing the information how the image was rated in average and how often.
	*/
	public function get_image_rating($user_rating = false, $display_contest_end = true)
	{
		global $template;
		$template->assign_var('GALLERY_RATING', self::MODE_SELECT);//@todo: phpbb_ext_gallery_core_config::get('rating_mode'));

		switch (self::MODE_SELECT)//@todo: phpbb_ext_gallery_core_config::get('rating_mode'))
		{
			//@todo: self::MODE_THUMB:
			//@todo: self::MODE_STARS:
			case self::MODE_SELECT:
			default:
				global $user;
				if ($this->image_data('image_contest'))
				{
					if (!$display_contest_end)
					{
						return $user->lang['CONTEST_RATING_HIDDEN'];
					}
					return $user->lang('CONTEST_RESULT_HIDDEN', $user->format_date(($this->album_data('contest_start') + $this->album_data('contest_end')), false, true));
				}
				else
				{
					if ($user_rating)
					{
						return $user->lang('RATING_STRINGS_USER', (int) $this->image_data('image_rates'), $this->get_image_rating_value(), $user_rating);
					}
					return $user->lang('RATING_STRINGS', (int) $this->image_data('image_rates'), $this->get_image_rating_value());
				}
			break;
		}
	}

	/**
	* Get rated value for a image
	*/
	private function get_image_rating_value()
	{
		if (phpbb_ext_gallery_core_contest::$mode == phpbb_ext_gallery_core_contest::MODE_SUM)
		{
			return $this->image_data('image_rate_points');
		}
		else
		{
			return ($this->image_data('image_rate_avg') / 100);
		}
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
		global $user, $phpbb_ext_gallery;
		return $phpbb_ext_gallery->auth->acl_check('i_rate', $this->album_data('album_id'), $this->album_data('album_user_id')) &&
			($user->data['user_id'] != $this->image_data('image_user_id')) && ($user->data['user_id'] != ANONYMOUS) &&
			($this->album_data('album_status') != phpbb_ext_gallery_core_album::STATUS_LOCKED) && ($this->image_data('image_status') == phpbb_ext_gallery_core_image::STATUS_APPROVED);
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
		global $user;
		return $this->is_allowed() && phpbb_ext_gallery_core_contest::is_step('rate', $this->album_data(true));
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

		global $db;

		$sql = 'SELECT rate_point
			FROM ' . GALLERY_RATES_TABLE . '
			WHERE rate_image_id = ' . $this->image_id . '
				AND rate_user_id = ' . $user_id;
		$result = $db->sql_query($sql);
		$rating = $db->sql_fetchfield('rate_point');
		$db->sql_freeresult($result);

		$this->user_rating[$user_id] = (is_bool($rating)) ? $rating : (int) $rating;
		return $this->user_rating[$user_id];
	}

	/**
	* Submit rating for an image.
	*
	* @param	int		$user_id
	* @param	int		$points
	* @param	string	$user_ip	Can be empty, function falls back to $user->ip
	*/
	public function submit_rating($user_id = false, $points = false, $user_ip = false)
	{
		switch (self::MODE_SELECT)//@todo: phpbb_ext_gallery_core_config::get('rating_mode'))
		{
			//@todo: self::MODE_THUMB:
			//@todo: self::MODE_STARS:
			case self::MODE_SELECT:
			default:
				global $user;

				$user_id = ($user_id) ? $user_id : $user->data['user_id'];
				$points = ($points) ? $points : request_var('rating', 0);
				$points = max(1, min($points, phpbb_ext_gallery_core_config::get('max_rating')));
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
	* @param	int		$user_id
	* @param	int		$points
	* @param	string	$user_ip	Can be empty, function falls back to $user->ip
	*/
	private function insert_rating($user_id, $points, $user_ip = false)
	{
		global $db, $user;

		$sql_ary = array(
			'rate_image_id'	=> $this->image_id,
			'rate_user_id'	=> $user_id,
			'rate_user_ip'	=> ($user_ip) ? $user_ip : $user->ip,
			'rate_point'	=> $points,
		);
		$db->sql_query('INSERT INTO ' . GALLERY_RATES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary));
	}

	/**
	* Recalculate the average image-rating and such stuff.
	*
	* @param	mixed	$image_ids	Array or integer with image_id where we recalculate the rating.
	*/
	static public function recalc_image_rating($image_ids)
	{
		global $db;

		if (is_array($image_ids))
		{
			$image_ids = array_map('intval', $image_ids);
		}
		else
		{
			$image_ids = (int) $image_ids;
		}

		$sql = 'SELECT rate_image_id, COUNT(rate_user_ip) image_rates, AVG(rate_point) image_rate_avg, SUM(rate_point) image_rate_points
			FROM ' . GALLERY_RATES_TABLE . '
			WHERE ' . $db->sql_in_set('rate_image_id', $image_ids, false, true) . '
			GROUP BY rate_image_id';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
				SET image_rates = ' . $row['image_rates'] . ',
					image_rate_points = ' . $row['image_rate_points'] . ',
					image_rate_avg = ' . round($row['image_rate_avg'], 2) * 100 . '
				WHERE image_id = ' . $row['rate_image_id'];
			$db->sql_query($sql);
		}
		$db->sql_freeresult($result);
	}

	/**
	* Delete all ratings for given image_ids
	*
	* @param	mixed	$image_ids		Array or integer with image_id where we delete the rating.
	* @param	bool	$reset_average	Shall we also reset the average? We can save that query, when the images are deleted anyway.
	*/
	static public function delete_ratings($image_ids, $reset_average = false)
	{
		global $db;

		if (is_array($image_ids))
		{
			$image_ids = array_map('intval', $image_ids);
		}
		else
		{
			$image_ids = (int) $image_ids;
		}

		$sql = 'DELETE FROM ' . GALLERY_RATES_TABLE . '
			WHERE ' . $db->sql_in_set('rate_image_id', $image_ids, false, true);
		$result = $db->sql_query($sql);

		if ($reset_average)
		{
			$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . '
				SET image_rates = 0,
					image_rate_points = 0,
					image_rate_avg = 0
				WHERE ' . $db->sql_in_set('image_id', $image_ids);
			$db->sql_query($sql);
		}
	}
}
