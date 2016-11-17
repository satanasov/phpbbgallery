<?php

/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\file;

/**
 * A little class for all the actions that the gallery does on images.
*
* resize, rotate, watermark, crete thumbnail, write to hdd, send to browser
 *
 * @property \phpbbgallery\core\url url
 * @property \phpbb\request\request request
 */
class file
{
	const THUMBNAIL_INFO_HEIGHT = 16;
	const GDLIB1 = 1;
	const GDLIB2 = 2;

	public $chmod = 0644;

	public $errors = array();
	private $browser_cache = true;
	private $last_modified = 0;

	public $gd_version = 0;

	public $image;
	public $image_content_type;
	public $image_name = '';
	public $image_quality = 100;
	public $image_size = array();
	public $image_source = '';
	public $image_type;

	public $max_file_size = 0;
	public $max_height = 0;
	public $max_width = 0;

	public $resized = false;
	public $rotated = false;

	public $thumb_height = 0;
	public $thumb_width = 0;

	public $watermark;
	public $watermark_size = array();
	public $watermark_source = '';
	public $watermarked = false;

	/**
	 * Constructor - init some basic stuff
	 *
	 * @param \phpbb\request\request $request
	 * @param \phpbbgallery\core\url $url
	 * @param \phpbbgallery\core\config $gallery_config
	 * @param int $gd_version
	 */
	public function __construct(\phpbb\request\request $request, \phpbbgallery\core\url $url, \phpbbgallery\core\config $gallery_config, $gd_version)
	{
		$this->request = $request;
		$this->url = $url;
		$this->gallery_config = $gallery_config;
		$this->gd_version = $gd_version;
	}

	public function set_image_options($max_file_size, $max_height, $max_width)
	{
		$this->max_file_size = $max_file_size;
		$this->max_height = $max_height;
		$this->max_width = $max_width;
	}

	public function set_image_data($source = '', $name = '', $size = 0, $force_empty_image = false)
	{
		if ($source)
		{
			$this->image_source = $source;
		}
		if ($name)
		{
			$this->image_name = $name;
		}
		if ($size)
		{
			$this->image_size['file'] = $size;
		}
		if ($force_empty_image)
		{
			$this->image = null;
			$this->watermarked = false;
			$this->rotated = false;
			$this->resized = false;
		}
	}

	/**
	 * Get image mimetype by filename
	 *
	 * Only use this, if the image is secure. As we created all these images, they should be...
	 * @param $filename
	 * @return string
	 */
	static public function mimetype_by_filename($filename)
	{
		switch (utf8_substr(strtolower($filename), -4))
		{
			case '.png':
				return 'image/png';
			break;
			case '.gif':
				return 'image/gif';
			break;
			case 'jpeg':
			case '.jpg':
				return 'image/jpeg';
			break;
		}

		return '';
	}

	/**
	 * Read image
	 * @param bool $force_filesize
	 * @return bool
	 */
	public function read_image($force_filesize = false)
	{
		if (!file_exists($this->image_source))
		{
			return false;
		}

		switch (utf8_substr(strtolower($this->image_source), -4))
		{
			case '.png':
				$this->image = imagecreatefrompng($this->image_source);
				imagealphablending($this->image, true); // Set alpha blending on ...
				imagesavealpha($this->image, true); // ... and save alphablending!
				$this->image_type = 'png';
			break;
			case '.gif':
				$this->image = imagecreatefromgif($this->image_source);
				$this->image_type = 'gif';
			break;
			default:
				$this->image = imagecreatefromjpeg($this->image_source);
				$this->image_type = 'jpeg';
			break;
		}

		$file_size = 0;
		if (isset($this->image_size['file']))
		{
			$file_size = $this->image_size['file'];
		}
		else if ($force_filesize)
		{
			$file_size = @filesize($this->image_source);
		}

		$image_size = getimagesize($this->image_source);

		$this->image_size['file'] = $file_size;
		$this->image_size['width'] = $image_size[0];
		$this->image_size['height'] = $image_size[1];

		$this->image_content_type = $image_size['mime'];
	}

	/**
	 * Write image to disk
	 * @param $destination
	 * @param int $quality
	 * @param bool $destroy_image
	 */
	public function write_image($destination, $quality = -1, $destroy_image = false)
	{
		if ($quality == -1)
		{
			$quality = $this->gallery_config->get('jpg_quality');
		}
		switch ($this->image_type)
		{
			case 'jpeg':
				imagejpeg($this->image, $destination, $quality);
			break;
			case 'png':
				imagepng($this->image, $destination);
			break;
			case 'gif':
				imagegif($this->image, $destination);
			break;
		}
		@chmod($destination, $this->chmod);

		if ($destroy_image)
		{
			imagedestroy($this->image);
		}
	}

	/**
	 * Get a browser friendly UTF-8 encoded filename
	 *
	 * @param $file
	 * @return string
	 */
	public function header_filename($file)
	{
		$raw = $this->request->server('HTTP_USER_AGENT');
		$user_agent = htmlspecialchars($raw);

		// There be dragons here.
		// Not many follows the RFC...
		if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Safari') !== false || strpos($user_agent, 'Konqueror') !== false)
		{
			return "filename=" . rawurlencode($file);
		}

		// follow the RFC for extended filename for the rest
		return "filename*=UTF-8''" . rawurlencode($file);
	}

	/**
	* We need to disable the "last-modified" caching for guests and in cases of image-errors,
	* so that they can view them, if they logged in or the error was fixed.
	*/
	public function disable_browser_cache()
	{
		$this->browser_cache = false;
	}

	/**
	 * Collect the last timestamp where something changed.
	 * This must contain:
	 *    - Last change of the file
	 *    - Last change of user's permissions
	 *    - Last change of user's groups
	 *    - Last change of watermark config
	 *    - Last change of watermark file
	 * @param $timestamp
	 */
	public function set_last_modified($timestamp)
	{
		$this->last_modified = max($timestamp, $this->last_modified);
	}

	static public function is_ie_greater7($browser)
	{
		return (bool) preg_match('/msie (\d{2,3}|[89]+).[0-9.]*;/', strtolower($browser));
	}

	public function create_thumbnail($max_width, $max_height, $print_details = false, $additional_height = 0, $image_size = array())
	{
		$this->resize_image($max_width, $max_height, (($print_details) ? $additional_height : 0));

		// Create image details credits to Dr.Death
		if ($print_details && sizeof($image_size))
		{
			$dimension_font = 1;
			$dimension_string = $image_size['width'] . "x" . $image_size['height'] . "(" . intval($image_size['file'] / 1024) . "KiB)";
			$dimension_colour = imagecolorallocate($this->image, 255, 255, 255);
			$dimension_height = imagefontheight($dimension_font);
			$dimension_width = imagefontwidth($dimension_font) * strlen($dimension_string);
			$dimension_x = ($this->image_size['width'] - $dimension_width) / 2;
			$dimension_y = $this->image_size['height'] + (($additional_height - $dimension_height) / 2);
			$black_background = imagecolorallocate($this->image, 0, 0, 0);
			imagefilledrectangle($this->image, 0, $this->thumb_height, $this->thumb_width, $this->thumb_height + $additional_height, $black_background);
			imagestring($this->image, 1, $dimension_x, $dimension_y, $dimension_string, $dimension_colour);
		}
	}

	public function resize_image($max_width, $max_height, $additional_height = 0)
	{
		if (!$this->image)
		{
			$this->read_image();
		}

		if (($this->image_size['height'] <= $max_height) && ($this->image_size['width'] <= $max_width))
		{
			// image is small enough, nothing to do here.
			return;
		}

		if (($this->image_size['height'] / $max_height) > ($this->image_size['width'] / $max_width))
		{
			$this->thumb_height	= $max_height;
			$this->thumb_width	= round($max_width * (($this->image_size['width'] / $max_width) / ($this->image_size['height'] / $max_height)));
		}
		else
		{
			$this->thumb_height	= round($max_height * (($this->image_size['height'] / $max_height) / ($this->image_size['width'] / $max_width)));
			$this->thumb_width	= $max_width;
		}

		$image_copy = (($this->gd_version == self::GDLIB1) ? @imagecreate($this->thumb_width, $this->thumb_height + $additional_height) : @imagecreatetruecolor($this->thumb_width, $this->thumb_height + $additional_height));
		if ($this->image_type != 'jpeg')
		{
			imagealphablending($image_copy, false);
			imagesavealpha($image_copy, true);
			$transparent = imagecolorallocatealpha($image_copy, 255, 255, 255, 127);
			imagefilledrectangle($image_copy, 0, 0, $this->thumb_width, $this->thumb_height + $additional_height, $transparent);
		}

		$resize_function = ($this->gd_version == self::GDLIB1) ? 'imagecopyresized' : 'imagecopyresampled';
		$resize_function($image_copy, $this->image, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $this->image_size['width'], $this->image_size['height']);

		imagealphablending($image_copy, true);
		imagesavealpha($image_copy, true);
		$this->image = $image_copy;

		$this->image_size['height'] = $this->thumb_height;
		$this->image_size['width'] = $this->thumb_width;

		$this->resized = true;
	}

	/**
	 * Rotate the image
	 * Usage optimized for 0�, 90�, 180� and 270� because of the height and width
	 *
	 * @param $angle
	 * @param $ignore_dimensions
	 */
	public function rotate_image($angle, $ignore_dimensions)
	{
		if (!function_exists('imagerotate'))
		{
			$this->errors[] = array('ROTATE_IMAGE_FUNCTION', $angle);
			return;
		}
		if (($angle <= 0) || (($angle % 90) != 0))
		{
			$this->errors[] = array('ROTATE_IMAGE_ANGLE', $angle);
			return;
		}

		if (!$this->image)
		{
			$this->read_image();
		}
		if ((($angle / 90) % 2) == 1)
		{
			// Left or Right, we need to switch the height and width
			if (!$ignore_dimensions && (($this->image_size['height'] > $this->max_width) || ($this->image_size['width'] > $this->max_height)))
			{
				// image would be to wide/high
				if ($this->image_size['height'] > $this->max_width)
				{
					$this->errors[] = array('ROTATE_IMAGE_WIDTH');
				}
				if ($this->image_size['width'] > $this->max_height)
				{
					$this->errors[] = array('ROTATE_IMAGE_HEIGHT');
				}
				return;
			}
			$new_width = $this->image_size['height'];
			$this->image_size['height'] = $this->image_size['width'];
			$this->image_size['width'] = $new_width;
		}
		$this->image = imagerotate($this->image, $angle, 0);

		$this->rotated = true;
	}

	/**
	 * Watermark the image:
	 *
	 * @param $watermark_source
	 * @param int $watermark_position summary of the parameters for vertical and horizontal adjustment
	 * @param int $min_height
	 * @param int $min_width
	 */
	public function watermark_image($watermark_source, $watermark_position = 20, $min_height = 0, $min_width = 0)
	{
		$this->watermark_source = $watermark_source;
		if (!$this->watermark_source || !file_exists($this->watermark_source))
		{
			$this->errors[] = array('WATERMARK_IMAGE_SOURCE');
			return;
		}

		if (!$this->image)
		{
			$this->read_image();
		}

		if (($min_height && ($this->image_size['height'] < $min_height)) || ($min_width && ($this->image_size['width'] < $min_width)))
		{
			return;
			//$this->errors[] = array('WATERMARK_IMAGE_DIMENSION');
		}
		$get_dot = strrpos($this->image_source, '.');
		$get_wm_name = substr_replace($this->image_source, '_wm', $get_dot, 0);
		if (file_exists($get_wm_name))
		{
			$this->image_source = $get_wm_name;
			$this->read_image();
		}
		else
		{
			$this->watermark_size = getimagesize($this->watermark_source);
			switch ($this->watermark_size['mime'])
			{
				case 'image/png':
					$imagecreate = 'imagecreatefrompng';
					break;
				case 'image/gif':
					$imagecreate = 'imagecreatefromgif';
					break;
				default:
					$imagecreate = 'imagecreatefromjpeg';
					break;
			}

			// Get the watermark as resource.
			if (($this->watermark = $imagecreate($this->watermark_source)) === false)
			{
				$this->errors[] = array('WATERMARK_IMAGE_IMAGECREATE');
			}

			$phpbb_gallery_constants = new \phpbbgallery\core\constants();
			// Where do we display the watermark? up-left, down-right, ...?
			$dst_x = (($this->image_size['width'] * 0.5) - ($this->watermark_size[0] * 0.5));
			$dst_y = ($this->image_size['height'] - $this->watermark_size[1] - 5);
			if ($watermark_position & $phpbb_gallery_constants::WATERMARK_LEFT)
			{
				$dst_x = 5;
			}
			else if ($watermark_position & $phpbb_gallery_constants::WATERMARK_RIGHT)
			{
				$dst_x = ($this->image_size['width'] - $this->watermark_size[0] - 5);
			}
			if ($watermark_position & $phpbb_gallery_constants::WATERMARK_TOP)
			{
				$dst_y = 5;
			}
			else if ($watermark_position & $phpbb_gallery_constants::WATERMARK_MIDDLE)
			{
				$dst_y = (($this->image_size['height'] * 0.5) - ($this->watermark_size[1] * 0.5));
			}
			imagecopy($this->image, $this->watermark, $dst_x, $dst_y, 0, 0, $this->watermark_size[0], $this->watermark_size[1]);
			imagedestroy($this->watermark);
			$this->write_image($get_wm_name);
			$this->image_source = $get_wm_name;
			$this->read_image();
		}
		$this->watermarked = true;
	}

	/**
	* Delete file from disc.
	*
	* @param	mixed		$files		String with filename or an array of filenames
	*									Array-Format: $image_id => $filename
	* @param	array		$locations	Array of valid url::path()s where the image should be deleted from
	*/
	public function delete($files, $locations = array('thumbnail', 'medium', 'upload'))
	{
		if (!is_array($files))
		{
			$files = array(1 => $files);
		}
		// Let's delete watermarked
		$this->delete_wm($files);
		foreach ($files as $image_id => $file)
		{
			foreach ($locations as $location)
			{
				@unlink($this->url->path($location) . $file);
			}
		}
	}

	/**
	 * @param $files
	 * @param array $locations
	 */
	public function delete_cache($files, $locations = array('thumbnail', 'medium'))
	{
		if (!is_array($files))
		{
			$files = array(1 => $files);
		}
		$this->delete_wm($files);
		foreach ($files as $image_id => $file)
		{
			foreach ($locations as $location)
			{
				@unlink($this->url->path($location) . $file);
			}
		}
	}

	/**
	 * @param $files
	 */
	public function delete_wm($files)
	{
		$locations = array('upload', 'medium');
		if (!is_array($files))
		{
			$files = array(1 => $files);
		}
		foreach ($files as $image_id => $file)
		{
			$get_dot = strrpos($file, '.');
			$get_wm_name = substr_replace($file, '_wm', $get_dot, 0);
			foreach ($locations as $location)
			{
				@unlink($this->url->path($location) . $get_wm_name);
			}
		}
	}
}
