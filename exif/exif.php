<?php
/**
*
* @package Gallery - Exif Extension
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\exif;

/**
* Base class for Exif handling
*/
class exif
{
	/**
	* Default value for new users
	*/
	const DEFAULT_DISPLAY	= true;

	/**
	* phpBB will treat the time from the Exif data like UTC.
	* If your images were taken with an other timezone, you can insert an offset here.
	* The offset is than added to the timestamp before it is converted into the users time.
	*
	* Offset must be set in seconds.
	*/
	const TIME_OFFSET	= 0;

	/**
	* Constants for the status of the Exif data.
	*/
	const UNAVAILABLE	= 0;
	const AVAILABLE		= 1;
	const UNKNOWN		= 2;
	const DBSAVED		= 3;

	/**
	* Is the function available?
	*/
	static public $function_exists = null;

	/**
	* Exif data array with all allowed groups and keys.
	*/
	public $data		= array();

	/**
	* Filtered data array. We don't have empty or invalid values here.
	*/
	public $prepared_data	= array();

	/**
	* Does the image have exif data?
	* Values see constant declaration at the beginning of the class.
	*/
	public $status		= 2;

	/**
	* Full data array, but serialized to a string
	*/
	public $serialized	= '';

	/**
	* Full link to the image-file
	*/
	public $file		= '';

	/**
	* Image-ID, just needed to update the Exif status
	*/
	public $image_id	= false;

	/**
	* Constructor
	*
	* @param	string	$file		Full link to the image-file
	* @param	mixed	$image_id	False or integer
	*/
	public function __construct($file, $image_id = false)
	{
		if (self::$function_exists === null)
		{
			self::$function_exists = (function_exists('exif_read_data')) ? true : false;
		}
		if ($image_id)
		{
			$this->image_id = (int) $image_id;
		}

		$this->file = $file;
	}

	/**
	* Intepret the values from the database, and read the data if we don't have it.
	*
	* @param	int		$status		Value of a status constant (see beginning of the class)
	* @param	mixed	$data		Either an empty string or the serialized array of the Exif from the database
	*/
	public function interpret($status, $data)
	{
		$this->orig_status = $status;
		$this->status = $status;
		if ($this->status == self::DBSAVED)
		{
			$this->data = unserialize($data);
		}
		else if (($this->status == self::AVAILABLE) || ($this->status == self::UNKNOWN))
		{
			$this->read();
		}
	}

	/**
	* Read Exif data from the image
	*/
	public function read()
	{
		if (!self::$function_exists || !$this->file || !file_exists($this->file))
		{
			return;
		}

		$this->data = @exif_read_data($this->file, 0, true);

		if (!empty($this->data["EXIF"]))
		{
			// Unset invalid Exifs
			foreach ($this->data as $key => $array)
			{
				if (!in_array($key, self::$allowed_groups))
				{
					unset($this->data[$key]);
				}
				else
				{
					foreach ($this->data[$key] as $subkey => $array)
					{
						if (!in_array($subkey, self::$allowed_keys))
						{
							unset($this->data[$key][$subkey]);
						}
					}
				}
			}

			$this->serialized = serialize($this->data);
			$this->status = self::DBSAVED;
		}
		else
		{
			$this->status = self::UNAVAILABLE;
		}

		if ($this->image_id)
		{
			$this->set_status();
		}
	}

	/**
	* Validate and prepare the data, so we can send it into the template.
	*/
	private function prepare_data()
	{
		global $user;

		$user->add_lang_ext('phpbbgallery/exif', 'exif');

		$this->prepared_data = array();
		if (isset($this->data["EXIF"]["DateTimeOriginal"]))
		{
			$timestamp_year = substr($this->data["EXIF"]["DateTimeOriginal"], 0, 4);
			$timestamp_month = substr($this->data["EXIF"]["DateTimeOriginal"], 5, 2);
			$timestamp_day = substr($this->data["EXIF"]["DateTimeOriginal"], 8, 2);
			$timestamp_hour = substr($this->data["EXIF"]["DateTimeOriginal"], 11, 2);
			$timestamp_minute = substr($this->data["EXIF"]["DateTimeOriginal"], 14, 2);
			$timestamp_second = substr($this->data["EXIF"]["DateTimeOriginal"], 17, 2);
			$timestamp = (int) @mktime($timestamp_hour, $timestamp_minute, $timestamp_second, $timestamp_month, $timestamp_day, $timestamp_year);
			if ($timestamp)
			{
				$this->prepared_data['exif_date'] = $user->format_date($timestamp + self::TIME_OFFSET);
			}
		}
		if (isset($this->data["EXIF"]["FocalLength"]))
		{
			list($num, $den) = explode("/", $this->data["EXIF"]["FocalLength"]);
			if ($den)
			{
				$this->prepared_data['exif_focal'] = sprintf($user->lang['EXIF_FOCAL_EXP'], ($num / $den));
			}
		}
		if (isset($this->data["EXIF"]["ExposureTime"]))
		{
			list($num, $den) = explode("/", $this->data["EXIF"]["ExposureTime"]);
			$exif_exposure = '';
			if (($num > $den) && $den)
			{
				$exif_exposure = $num / $den;
			}
			else if ($num)
			{
				$exif_exposure = ' 1/' . $den / $num ;
			}
			if ($exif_exposure)
			{
				$this->prepared_data['exif_exposure'] = sprintf($user->lang['EXIF_EXPOSURE_EXP'], $exif_exposure);
			}
		}
		if (isset($this->data["EXIF"]["FNumber"]))
		{
			list($num, $den) = explode("/", $this->data["EXIF"]["FNumber"]);
			if ($den)
			{
				$this->prepared_data['exif_aperture'] = "F/" . ($num / $den);
			}
		}
		if (isset($this->data["EXIF"]["ISOSpeedRatings"]) && !is_array($this->data["EXIF"]["ISOSpeedRatings"]))
		{
			$this->prepared_data['exif_iso'] = $this->data["EXIF"]["ISOSpeedRatings"];
		}
		if (isset($this->data["EXIF"]["WhiteBalance"]))
		{
			$this->prepared_data['exif_whiteb'] = $user->lang['EXIF_WHITEB_' . (($this->data["EXIF"]["WhiteBalance"]) ? 'MANU' : 'AUTO')];
		}
		if (isset($this->data["EXIF"]["Flash"]))
		{
			if (isset($user->lang['EXIF_FLASH_CASE_' . $this->data["EXIF"]["Flash"]]))
			{
				$this->prepared_data['exif_flash'] = $user->lang['EXIF_FLASH_CASE_' . $this->data["EXIF"]["Flash"]];
			}
		}
		if (isset($this->data["IFD0"]["Model"]))
		{
			$this->prepared_data['exif_cam_model'] = ucwords($this->data["IFD0"]["Model"]);
		}
		if (isset($this->data["EXIF"]["ExposureProgram"]))
		{
			if (isset($user->lang['EXIF_EXPOSURE_PROG_' . $this->data["EXIF"]["ExposureProgram"]]))
			{
				$this->prepared_data['exif_exposure_prog'] = $user->lang['EXIF_EXPOSURE_PROG_' . $this->data["EXIF"]["ExposureProgram"]];
			}
		}
		if (isset($this->data["EXIF"]["ExposureBiasValue"]))
		{
			list($num,$den) = explode("/", $this->data["EXIF"]["ExposureBiasValue"]);
			if ($den)
			{
				if (($num / $den) == 0)
				{
					$exif_exposure_bias = 0;
				}
				else
				{
					$exif_exposure_bias = $this->data["EXIF"]["ExposureBiasValue"];
				}
				$this->prepared_data['exif_exposure_bias'] = sprintf($user->lang['EXIF_EXPOSURE_BIAS_EXP'], $exif_exposure_bias);
			}
		}
		if (isset($this->data["EXIF"]["MeteringMode"]))
		{
			if (isset($user->lang['EXIF_METERING_MODE_' . $this->data["EXIF"]["MeteringMode"]]))
			{
				$this->prepared_data['exif_metering_mode'] = $user->lang['EXIF_METERING_MODE_' . $this->data["EXIF"]["MeteringMode"]];
			}
		}
	}

	/**
	* Sends the Exif into the template
	*
	* @param	bool	$expand_view	Shall we expand the Exif data on page view or collapse?
	* @param	string	$block			Name of the template loop the Exifs are displayed in.
	*/
	public function send_to_template($expand_view = true, $block = 'exif_value')
	{
		$this->prepare_data();

		if (!empty($this->prepared_data))
		{
			global $template, $user;

			foreach ($this->prepared_data as $exif => $value)
			{
				$template->assign_block_vars($block, array(
					'EXIF_NAME'			=> $user->lang[strtoupper($exif)],
					'EXIF_VALUE'		=> htmlspecialchars($value),
				));
			}
			$template->assign_vars(array(
				'S_EXIF_DATA'	=> true,
				'S_VIEWEXIF'	=> $expand_view,
			));
		}
	}

	/**
	* Save the new Exif status in the database
	*/
	public function set_status()
	{
		if (!$this->image_id || ($this->orig_status == $this->status))
		{
			return false;
		}

		global $db, $table_prefix;

		$update_data = ($this->status == self::DBSAVED) ? ", image_exif_data = '" . $db->sql_escape($this->serialized) . "'" : '';
		$sql = 'UPDATE ' . $table_prefix . 'gallery_images 
			SET image_has_exif = ' . $this->status . $update_data . '
			WHERE image_id = ' . $this->image_id;
		$db->sql_query($sql);
	}

	/**
	* There are lots of possible Exif Groups and Values.
	* But you will never heard of the missing ones. so we just allow the most common ones.
	*/
	static private $allowed_groups		= array(
		'EXIF',
		'IFD0',
	);

	static private $allowed_keys		= array(
		'DateTimeOriginal',
		'FocalLength',
		'ExposureTime',
		'FNumber',
		'ISOSpeedRatings',
		'WhiteBalance',
		'Flash',
		'Model',
		'ExposureProgram',
		'ExposureBiasValue',
		'MeteringMode',
	);
}
