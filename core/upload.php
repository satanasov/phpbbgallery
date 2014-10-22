<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2011 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
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

class phpbb_ext_gallery_core_upload
{
	/**
	* Number of Files per Directory
	*
	* If this constant is set to a value >0 the gallery will create a new directory,
	* when the current directory has more files in it than set here.
	*/
	const NUM_FILES_PER_DIR = 0;

	/**
	* Objects: phpBB Upload, 2 Files and Image-Functions
	*/
	private $upload = null;
	private $file = null;
	private $zip_file = null;
	private $tools = null;

	/**
	* Basic variables...
	*/
	public $loaded_files = 0;
	public $uploaded_files = 0;
	public $errors = array();
	public $images = array();
	public $image_data = array();
	public $array_id2row = array();
	private $album_id = 0;
	private $file_count = 0;
	private $image_num = 0;
	private $allow_comments = false;
	private $sent_quota_error = false;
	private $username = '';
	private $file_descriptions = array();
	private $file_names = array();
	private $file_rotating = array();

	/**
	* Constructor
	*/
	public function __construct($album_id, $num_files = 0)
	{
		global $user, $phpbb_ext_gallery;

		if (!class_exists('fileupload'))
		{
			$phpbb_ext_gallery->url->_include('functions_upload', 'phpbb');
		}
		$this->upload = new fileupload();
		$this->upload->fileupload('', self::get_allowed_types(), (4 * $phpbb_ext_gallery->config->get('max_filesize')));

		$this->tools = new phpbb_ext_gallery_core_file($phpbb_ext_gallery->config->get('gdlib_version'));

		$this->album_id = (int) $album_id;
		$this->file_limit = (int) $num_files;
		$this->username = $user->data['username'];
	}

	/**
	* Upload a file and then call the function for reading the zip or preparing the image
	*/
	public function upload_file($file_count)
	{
		if ($this->file_limit && ($this->uploaded_files >= $this->file_limit))
		{
			$this->quota_error();
			return false;
		}
		$this->file_count = (int) $file_count;
		$this->file = $this->upload->form_upload('image_file_' . $this->file_count);
		if (!$this->file->uploadname)
		{
			return false;
		}

		if ($this->file->extension == 'zip')
		{
			$this->zip_file = $this->file;
			$this->upload_zip();
		}
		else
		{
			$image_id = $this->prepare_file();

			if ($image_id)
			{
				$this->uploaded_files++;
				$this->images[] = (int) $image_id;
			}
		}
	}

	/**
	* Upload a zip file and save the images into the import/ directory.
	*/
	public function upload_zip()
	{
		global $phpbb_ext_gallery;

		if (!class_exists('compress_zip'))
		{
			$phpbb_ext_gallery->url->_include('functions_compress', 'phpbb');
		}

		global $user;
		$tmp_dir = $phpbb_ext_gallery->url->path('import') . 'tmp_' . md5(unique_id()) . '/';

		$this->zip_file->clean_filename('unique_ext'/*, $user->data['user_id'] . '_'*/);
		$this->zip_file->move_file(substr($phpbb_ext_gallery->url->path('import_noroot'), 0, -1), false, false, CHMOD_ALL);
		if (!empty($this->zip_file->error))
		{
			$this->zip_file->remove();
			$this->new_error($user->lang('UPLOAD_ERROR', $this->zip_file->uploadname, implode('<br />&raquo; ', $this->zip_file->error)));
			return false;
		}

		$compress = new compress_zip('r', $this->zip_file->destination_file);
		$compress->extract($tmp_dir);
		$compress->close();

		$this->zip_file->remove();

		// Remove zip from allowed extensions
		$this->upload->set_allowed_extensions(self::get_allowed_types(false, true));

		$this->read_zip_folder($tmp_dir);

		// Read zip from allowed extensions
		$this->upload->set_allowed_extensions(self::get_allowed_types());
	}

	/**
	* Read a folder from the zip, "upload" the images and remove the rest.
	*/
	public function read_zip_folder($current_dir)
	{
		$handle = opendir($current_dir);
		while ($file = readdir($handle))
		{
			if ($file == '.' || $file == '..') continue;
			if (is_dir($current_dir . $file))
			{
				$this->read_zip_folder($current_dir . $file . '/');
			}
			else if (in_array(utf8_substr(strtolower($file), utf8_strrpos($file, '.') + 1), self::get_allowed_types(false, true)))
			{
				if (!$this->file_limit || ($this->uploaded_files < $this->file_limit))
				{
					$this->file = $this->upload->local_upload($current_dir . $file);
					if ($this->file->error)
					{
						$this->new_error($user->lang('UPLOAD_ERROR', $this->file->uploadname, implode('<br />&raquo; ', $this->file->error)));
					}
					$image_id = $this->prepare_file();

					if ($image_id)
					{
						$this->uploaded_files++;
						$this->images[] = (int) $image_id;
					}
					else
					{
						if ($this->file->error)
						{
							$this->new_error($user->lang('UPLOAD_ERROR', $this->file->uploadname, implode('<br />&raquo; ', $this->file->error)));
						}
					}
				}
				else
				{
					$this->quota_error();
					@unlink($current_dir . $file);
				}

			}
			else
			{
				@unlink($current_dir . $file);
			}
		}
		closedir($handle);
		@rmdir($current_dir);
	}

	/**
	* Update image information in the database: name, description, status, contest, ...
	*/
	public function update_image($image_id, $needs_approval = false, $is_in_contest = false)
	{
		if ($this->file_limit && ($this->uploaded_files >= $this->file_limit))
		{
			global $user;
			$this->new_error($user->lang('UPLOAD_ERROR', $this->image_data[$image_id]['image_name'], $user->lang['QUOTA_REACHED']));
			return false;
		}
		$this->file_count = (int) $this->array_id2row[$image_id];

		$message_parser				= new parse_message();
		$message_parser->message	= utf8_normalize_nfc($this->get_description());
		if ($message_parser->message)
		{
			$message_parser->parse(true, true, true, true, false, true, true, true);
		}

		$sql_ary = array(
			'image_status'				=> ($needs_approval) ? phpbb_ext_gallery_core_image::STATUS_UNAPPROVED : phpbb_ext_gallery_core_image::STATUS_APPROVED,
			'image_contest'				=> ($is_in_contest) ? phpbb_ext_gallery_core_image::IN_CONTEST : phpbb_ext_gallery_core_image::NO_CONTEST,
			'image_desc'				=> $message_parser->message,
			'image_desc_uid'			=> $message_parser->bbcode_uid,
			'image_desc_bitfield'		=> $message_parser->bbcode_bitfield,
			'image_time'				=> time() + $this->file_count,
		);
		$new_image_name = $this->get_name();
		if (($new_image_name != '') && ($new_image_name != $this->image_data[$image_id]['image_name']))
		{
			$sql_ary = array_merge($sql_ary, array(
				'image_name'		=> $new_image_name,
				'image_name_clean'	=> utf8_clean_string($new_image_name),
			));
		}

		global $phpbb_dispatcher, $phpbb_ext_gallery;

		$additional_sql_data = array();
		$image_data = $this->image_data[$image_id];
		$file_link = $phpbb_ext_gallery->url->path('upload') . $this->image_data[$image_id]['image_filename'];

		$vars = array('additional_sql_data', 'image_data', 'file_link');
		extract($phpbb_dispatcher->trigger_event('gallery.core.upload.update_image_before', compact($vars)));

		// Rotate image
		if (!$this->prepare_file_update($image_id))
		{
			$vars = array('additional_sql_data');
			extract($phpbb_dispatcher->trigger_event('gallery.core.upload.update_image_nofilechange', compact($vars)));
		}

		$sql_ary = array_merge($sql_ary, $additional_sql_data);

		global $db;

		$sql = 'UPDATE ' . GALLERY_IMAGES_TABLE . ' 
			SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE image_id = ' . $image_id;
		$db->sql_query($sql);

		$this->uploaded_files++;

		return true;
	}

	/**
	* Prepare file on upload: rotate and resize
	*/
	public function prepare_file()
	{
		global $user, $phpbb_ext_gallery, $phpbb_dispatcher;

		$upload_dir = self::get_current_upload_dir();

		// Rename the file, move it to the correct location and set chmod
		if (!$upload_dir)
		{
			$this->file->clean_filename('unique_ext');
			$this->file->move_file(substr($phpbb_ext_gallery->url->path('upload_noroot'), 0, -1), false, false, CHMOD_ALL);
		}
		else
		{
			// Okay, this looks hacky, but what we do here is, we store the directory name in the filename.
			// However phpBB strips directories form the filename, when moving, so we need to specify that again.
			$this->file->clean_filename('unique_ext', $upload_dir . '/');
			$this->file->move_file($phpbb_ext_gallery->url->path('upload_noroot') . $upload_dir, false, false, CHMOD_ALL);
		}

		if (!empty($this->file->error))
		{
			$this->file->remove();
			$this->new_error($user->lang('UPLOAD_ERROR', $this->file->uploadname, implode('<br />&raquo; ', $this->file->error)));
			return false;
		}
		@chmod($this->file->destination_file, 0777);

		$additional_sql_data = array();
		$file = $this->file;

		$vars = array('additional_sql_data', 'file');
		extract($phpbb_dispatcher->trigger_event('gallery.core.upload.prepare_file_before', compact($vars)));

		$this->tools->set_image_options($phpbb_ext_gallery->config->get('max_filesize'), $phpbb_ext_gallery->config->get('max_height'), $phpbb_ext_gallery->config->get('max_width'));
		$this->tools->set_image_data($this->file->destination_file, '', $this->file->filesize, true);


		// Rotate the image
		if ($phpbb_ext_gallery->config->get('allow_rotate') && $this->get_rotating())
		{
			$this->tools->rotate_image($this->get_rotating(), $phpbb_ext_gallery->config->get('allow_resize'));
			if ($this->tools->rotated)
			{
				$this->file->height = $this->tools->image_size['height'];
				$this->file->width = $this->tools->image_size['width'];
			}
		}

		// Resize oversized images
		if (($this->file->width > $phpbb_ext_gallery->config->get('max_width')) || ($this->file->height > $phpbb_ext_gallery->config->get('max_height')))
		{
			if ($phpbb_ext_gallery->config->get('allow_resize'))
			{
				$this->tools->resize_image($phpbb_ext_gallery->config->get('max_width'), $phpbb_ext_gallery->config->get('max_height'));
				if ($this->tools->resized)
				{
					$this->file->height = $this->tools->image_size['height'];
					$this->file->width = $this->tools->image_size['width'];
				}
			}
			else
			{
				global $user;

				$this->file->remove();
				$this->new_error($user->lang('UPLOAD_ERROR', $this->file->uploadname, $user->lang['UPLOAD_IMAGE_SIZE_TOO_BIG']));
				return false;
			}
		}

		if ($this->file->filesize > (1.2 * $phpbb_ext_gallery->config->get('max_filesize')))
		{
			global $user;

			$this->file->remove();
			$this->new_error($user->lang('UPLOAD_ERROR', $this->file->uploadname, $user->lang['BAD_UPLOAD_FILE_SIZE']));
			return false;
		}

		if ($this->tools->rotated || $this->tools->resized)
		{
			$this->tools->write_image($this->file->destination_file, $phpbb_ext_gallery->config->get('jpg_quality'), true);
		}

		// Everything okay, now add the file to the database and return the image_id
		return $this->file_to_database($additional_sql_data);
	}

	/**
	* Prepare file on second upload step.
	* You can still rotate the image there.
	*/
	public function prepare_file_update($image_id)
	{
		global $phpbb_ext_gallery;

		$this->tools->set_image_options($phpbb_ext_gallery->config->get('max_filesize'), $phpbb_ext_gallery->config->get('max_height'), $phpbb_ext_gallery->config->get('max_width'));
		$this->tools->set_image_data($phpbb_ext_gallery->url->path('upload') . $this->image_data[$image_id]['image_filename'], '', 0, true);


		// Rotate the image
		if ($phpbb_ext_gallery->config->get('allow_rotate') && $this->get_rotating())
		{
			$this->tools->rotate_image($this->get_rotating(), $phpbb_ext_gallery->config->get('allow_resize'));
			if ($this->tools->rotated)
			{
				$this->tools->write_image($this->tools->image_source, $phpbb_ext_gallery->config->get('jpg_quality'), true);
				@unlink($phpbb_ext_gallery->url->path('thumbnail') . $this->image_data[$image_id]['image_filename']);
				@unlink($phpbb_ext_gallery->url->path('medium') . $this->image_data[$image_id]['image_filename']);
			}

		}

		return $this->tools->rotated;
	}

	/**
	* Insert the file into the database
	*/
	public function file_to_database($additional_sql_ary)
	{
		global $user, $db;

		$image_name = str_replace("_", " ", utf8_substr($this->file->uploadname, 0, utf8_strrpos($this->file->uploadname, '.')));

		$sql_ary = array_merge(array(
			'image_name'			=> $image_name,
			'image_name_clean'		=> utf8_clean_string($image_name),
			'image_filename' 		=> $this->file->realname,
			'filesize_upload'		=> $this->file->filesize,
			'image_time'			=> time() + $this->file_count,

			'image_user_id'			=> $user->data['user_id'],
			'image_user_colour'		=> $user->data['user_colour'],
			'image_username'		=> $this->username,
			'image_username_clean'	=> utf8_clean_string($this->username),
			'image_user_ip'			=> $user->ip,

			'image_album_id'		=> $this->album_id,
			'image_status'			=> phpbb_ext_gallery_core_image::STATUS_ORPHAN,
			'image_contest'			=> phpbb_ext_gallery_core_image::NO_CONTEST,
			'image_allow_comments'	=> $this->allow_comments,
			'image_desc'			=> '',
			'image_desc_uid'		=> '',
			'image_desc_bitfield'	=> '',
		), $additional_sql_ary);

		$sql = 'INSERT INTO ' . GALLERY_IMAGES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);

		$image_id = (int) $db->sql_nextid();
		$this->image_data[$image_id] = $sql_ary;

		return $image_id;
	}

	/**
	* Delete orphan uploaded files, which are older than half an hour...
	*/
	static public function prune_orphan($time = 0)
	{
		global $db;
		$prunetime = (int) (($time) ? $time : (time() - 1800));

		$sql = 'SELECT image_id, image_filename
			FROM ' . GALLERY_IMAGES_TABLE . '
			WHERE image_status = ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
				AND image_time < ' . $prunetime;
		$result = $db->sql_query($sql);
		$images = $filenames = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$images[] = (int) $row['image_id'];
			$filenames[(int) $row['image_id']] = $row['image_filename'];
		}
		$db->sql_freeresult($result);

		if ($images)
		{
			phpbb_ext_gallery_core_image::delete_images($images, $filenames, false);
		}
	}

	static private function get_current_upload_dir()
	{
		global $phpbb_ext_gallery;

		if (self::NUM_FILES_PER_DIR <= 0)
		{
			return 0;
		}

		$phpbb_ext_gallery->config->inc('current_upload_dir_size', 1);
		if ($phpbb_ext_gallery->config->get('current_upload_dir_size') >= self::NUM_FILES_PER_DIR)
		{
			$phpbb_ext_gallery->config->set('current_upload_dir_size', 0, true);
			$phpbb_ext_gallery->config->inc('current_upload_dir', 1);
			mkdir($phpbb_ext_gallery->url->path('upload') . $phpbb_ext_gallery->config->get('current_upload_dir'));
			mkdir($phpbb_ext_gallery->url->path('medium') . $phpbb_ext_gallery->config->get('current_upload_dir'));
			mkdir($phpbb_ext_gallery->url->path('thumbnail') . $phpbb_ext_gallery->config->get('current_upload_dir'));
			copy($phpbb_ext_gallery->url->path('upload') . 'index.htm', $phpbb_ext_gallery->url->path('upload') . $phpbb_ext_gallery->config->get('current_upload_dir') . '/index.htm');
			copy($phpbb_ext_gallery->url->path('upload') . 'index.htm', $phpbb_ext_gallery->url->path('medium') . $phpbb_ext_gallery->config->get('current_upload_dir') . '/index.htm');
			copy($phpbb_ext_gallery->url->path('upload') . 'index.htm', $phpbb_ext_gallery->url->path('thumbnail') . $phpbb_ext_gallery->config->get('current_upload_dir') . '/index.htm');
			copy($phpbb_ext_gallery->url->path('upload') . '.htaccess', $phpbb_ext_gallery->url->path('upload') . $phpbb_ext_gallery->config->get('current_upload_dir') . '/.htaccess');
			copy($phpbb_ext_gallery->url->path('upload') . '.htaccess', $phpbb_ext_gallery->url->path('medium') . $phpbb_ext_gallery->config->get('current_upload_dir') . '/.htaccess');
			copy($phpbb_ext_gallery->url->path('upload') . '.htaccess', $phpbb_ext_gallery->url->path('thumbnail') . $phpbb_ext_gallery->config->get('current_upload_dir') . '/.htaccess');
		}
		return $phpbb_ext_gallery->config->get('current_upload_dir');
	}

	public function quota_error()
	{
		if ($this->sent_quota_error) return;

		global $user;
		$this->new_error($user->lang('USER_REACHED_QUOTA_SHORT', $this->file_limit));
		$this->sent_quota_error = true;
	}

	public function new_error($error_msg)
	{
		$this->errors[] = $error_msg;
	}

	public function set_file_limit($num_files)
	{
		$this->file_limit = (int) $num_files;
	}

	public function set_username($username)
	{
		$this->username = $username;
	}

	public function set_rotating($data)
	{
		$this->file_rotating = array_map('intval', $data);
	}

	public function set_allow_comments($value)
	{
		$this->allow_comments = $value;
	}

	public function set_descriptions($descs)
	{
		$this->file_descriptions = $descs;
	}

	public function set_names($names)
	{
		$this->file_names = $names;
	}

	public function set_image_num($num)
	{
		$this->image_num = (int) $num;
	}

	public function use_same_name($use_same_name)
	{
		if ($use_same_name)
		{
			$image_name = $this->file_names[0];
			$image_desc = $this->file_descriptions[0];
			for ($i = 0; $i < sizeof($this->file_names); $i++)
			{
				$this->file_names[$i] = str_replace('{NUM}', ($this->image_num + $i), $image_name);
				$this->file_descriptions[$i] = str_replace('{NUM}', ($this->image_num + $i), $image_desc);
			}
		}
	}

	public function get_rotating()
	{
		if (!isset($this->file_rotating[$this->file_count]))
		{
			// If the template is still outdated, you'd get an error here...
			return 0;
		}
		if (($this->file_rotating[$this->file_count] % 90) != 0)
		{
			return 0;
		}
		return $this->file_rotating[$this->file_count];
	}

	public function get_name()
	{
		return utf8_normalize_nfc($this->file_names[$this->file_count]);
	}

	public function get_description()
	{
		if (!isset($this->file_descriptions[$this->file_count]))
		{
			// If the template is still outdated, you'd get a general error later...
			return '';
		}
		return utf8_normalize_nfc($this->file_descriptions[$this->file_count]);
	}

	public function get_images($uploaded_ids)
	{
		global $db;

		$image_ids = $filenames = array();
		foreach ($uploaded_ids as $row => $check)
		{
			if (strpos($check, '$') == false) continue;
			list($image_id, $filename) = explode('$', $check);
			$image_ids[] = (int) $image_id;
			$filenames[$image_id] = $filename;
			$this->array_id2row[$image_id] = $row;
		}

		if (empty($image_ids)) return;

		$sql = 'SELECT *
			FROM ' . GALLERY_IMAGES_TABLE . '
			WHERE image_status = ' . phpbb_ext_gallery_core_image::STATUS_ORPHAN . '
				AND ' . $db->sql_in_set('image_id', $image_ids);
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			if ($filenames[$row['image_id']] == substr($row['image_filename'], 0, 8))
			{
				$this->images[] = (int) $row['image_id'];
				$this->image_data[(int) $row['image_id']] = $row;
				$this->loaded_files++;
			}
		}
		$db->sql_freeresult($result);
	}

	/**
	* Get an array of allowed file types or file extensions
	*/
	static public function get_allowed_types($get_types = false, $ignore_zip = false)
	{
		global $user, $phpbb_ext_gallery;

		$extensions = $types = array();
		if ($phpbb_ext_gallery->config->get('allow_jpg'))
		{
			$types[] = $user->lang['FILETYPES_JPG'];
			$extensions[] = 'jpg';
			$extensions[] = 'jpeg';
		}
		if ($phpbb_ext_gallery->config->get('allow_gif'))
		{
			$types[] = $user->lang['FILETYPES_GIF'];
			$extensions[] = 'gif';
		}
		if ($phpbb_ext_gallery->config->get('allow_png'))
		{
			$types[] = $user->lang['FILETYPES_PNG'];
			$extensions[] = 'png';
		}
		if (!$ignore_zip && $phpbb_ext_gallery->config->get('allow_zip'))
		{
			$types[] = $user->lang['FILETYPES_ZIP'];
			$extensions[] = 'zip';
		}

		return ($get_types) ? $types : $extensions;
	}

	/**
	* Generate some kind of check so users only complete the uplaod for their images
	*/
	public function generate_hidden_fields()
	{
		$checks = array();
		foreach ($this->images as $image_id)
		{
			$checks[] = $image_id . '$' . substr($this->image_data[$image_id]['image_filename'], 0, 8);
		}
		return $checks;
	}
}
