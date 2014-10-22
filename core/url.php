<?php

/**
*
* @package phpBB Gallery
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core;

class url
{
	/**
	* Path from the gallery root, back to phpbb's root
	*/
	private $phpbb_root_path = '../';

	/**
	* Path from the phpbb root, into admin's root
	*/
	private $phpbb_admin_path = 'adm/';

	/**
	* Path from the phpbb root, into gallery's root
	*/
	private $phpbb_gallery_path = 'gallery/';

	/**
	* php-file extension
	*/
	private $phpEx = '.php';


	const IMAGE_PATH = 'images/';
	const UPLOAD_PATH = 'upload/';
	const THUMBNAIL_PATH = 'cache/';
	const MEDIUM_PATH = 'medium/';
	const IMPORT_PATH = 'import/';

	private $phpbb_gallery_relative = '';
	private $phpbb_gallery_full_path = '';

	/**
	* Constructor
	*/
	public function __construct($phpbb_root_path, $phpEx, $phpbb_admin_path = 'adm/')
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->phpbb_admin_path = $this->phpbb_root_path . $phpbb_admin_path;
		$this->phpEx = '.' . $phpEx;

		$this->phpbb_gallery_relative = self::beautiful_path($this->phpbb_root_path . $this->phpbb_gallery_path);
		$this->phpbb_gallery_full_path = self::beautiful_path(generate_board_url() . '/' . $this->phpbb_gallery_path, true);
	}

	public function path($directory = 'gallery')
	{
		switch ($directory)
		{
			case 'gallery':
				return $this->phpbb_gallery_relative;
			case 'ext':
				return $this->phpbb_root_path . 'ext/gallery/core/';
			case 'phpbb':
				return $this->phpbb_root_path;
			case 'admin':
				return $this->phpbb_admin_path;
			case 'relative':
				return $this->phpbb_gallery_path;
			case 'full':
				return $this->phpbb_gallery_full_path;
			case 'board':
				return generate_board_url() . '/';

			case 'images':
				return $this->phpbb_gallery_relative . self::IMAGE_PATH;
			case 'upload':
				return $this->phpbb_gallery_relative . self::IMAGE_PATH . self::UPLOAD_PATH;
			case 'thumbnail':
				return $this->phpbb_gallery_relative . self::IMAGE_PATH . self::THUMBNAIL_PATH;
			case 'medium':
				return $this->phpbb_gallery_relative . self::IMAGE_PATH . self::MEDIUM_PATH;
			case 'import':
				return $this->phpbb_gallery_relative . self::IMAGE_PATH . self::IMPORT_PATH;

				// stupid phpbb-upload class prepends the rootpath itself.
			case 'upload_noroot':
				return $this->phpbb_gallery_path . self::IMAGE_PATH . self::UPLOAD_PATH;
			case 'thumbnail_noroot':
				return $this->phpbb_gallery_path . self::IMAGE_PATH . self::THUMBNAIL_PATH;
			case 'medium_noroot':
				return $this->phpbb_gallery_path . self::IMAGE_PATH . self::MEDIUM_PATH;
			case 'import_noroot':
				return $this->phpbb_gallery_path . self::IMAGE_PATH . self::IMPORT_PATH;
		}

		return false;
	}

	public function append_sid()
	{
		$args = func_get_args();
		if (is_array($args[0]))
		{
			// Little problem from the duplicated call to func_get_args();
			$args = $args[0];
		}

		if (in_array($args[0], array('phpbb', 'admin', 'relative', 'full', 'board', 'ext')))
		{
			$mode = array_shift($args);
			$args[0] = $this->path($mode) . $this->phpEx_file($args[0]);
		}
		else
		{
			$args[0] = $this->path() . $this->phpEx_file($args[0]);
		}

		if (isset($args[1]))
		{
			$args[1] .= '';//@todo: phpbb_gallery::$display_popup;
		}

		$params = $args + array(
			0	=> '',
			1	=> '',//@todo: phpbb_gallery::$display_popup,
			2	=> true,
			3	=> false,
		);

		return append_sid($params[0], $params[1], $params[2], $params[3]);
	}

	/**
	* Removes the sid and replaces &amp; with normal &
	*/
	public function create_link($path, $file, $params = false, $is_amp = true)
	{
		if ($is_amp && !is_array($params))
		{
			$params = implode('&', explode('&amp;', $params));
		}

		return $this->append_sid($path, $file, $params, false, '');
	}

	public function redirect()
	{
		redirect($this->append_sid(func_get_args()));
	}

	public function phpEx_file($file)
	{
		if ((substr($file, -1) == '/') || (strlen($file) == 0))
		{
			// it's no file, so no .php here.
			return $file;
		}

		/*if ($file == 'image_page')
		{
			//@todo
			$file = 'viewimage';
		}*/

		return $file . $this->phpEx;
	}

	public function _include($file, $path = 'gallery', $sub_directory = 'includes/')
	{
		if (!is_array($file))
		{
			include($this->path($path) . $sub_directory . $this->phpEx_file($file));
		}
		else
		{
			foreach ($file as $real_file)
			{
				$this->_include($real_file, $path, $sub_directory);
			}
		}
	}

	public function _file_exists($file, $path = 'gallery', $sub_directory = 'includes/')
	{
		return file_exists($this->path($path) . $sub_directory . $this->phpEx_file($file));
	}

	public function _is_writable($file, $path = 'gallery', $sub_directory = 'includes/')
	{
		return phpbb_is_writable($this->path($path) . $sub_directory . $this->phpEx_file($file));
	}

	public function _return_file($file, $path = 'gallery', $sub_directory = 'includes/')
	{
		return $this->path($path) . $sub_directory . $this->phpEx_file($file);
	}

	/**
	* Creates beautiful relative path from ugly relative path
	* Resolves .. (up directory)
	*
	* @author	bantu		based on phpbb_own_realpath() by Chris Smith
	* @license	http://opensource.org/licenses/gpl-license.php GNU Public License
	*
	* @param	string		ugly path e.g. "../community/../gallery/"
	* @param	bool		is it a full url, so we need to fix teh http:// at the beginning?
	* @return	string		beautiful path e.g. "../gallery/"
	*/
	static public function beautiful_path($path, $is_full_url = false)
	{
		// Remove any repeated slashes
		$path = preg_replace('#/{2,}#', '/', $path);

		if ($is_full_url)
		{
			// Fix the double slash, which we just removed.
			if (strpos($path, 'https:/') === 0)
			{
				$path = 'https://' . substr($path, 7);
			}
			else if (strpos($path, 'http:/') === 0)
			{
				$path = 'http://' . substr($path, 6);
			}
		}

		// Break path into pieces
		$bits = explode('/', $path);

		// Lets get looping, run over and resolve any .. (up directory)
		for ($i = 0, $max = sizeof($bits); $i < $max; $i++)
		{
			if ($bits[$i] == '..' && isset($bits[$i - 1]) && $bits[$i - 1][0] != '.')
			{
				// We found a .. and we are able to traverse upwards ...
				unset($bits[$i]);
				unset($bits[$i - 1]);

				$i -= 2;
				$max -= 2;

				$bits = array_values($bits);
			}
		}

		return implode('/', $bits);
	}
}
