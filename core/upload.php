<?php
/**
*
* @package phpBB Gallery
* @version $Id$
* @copyright (c) 2011 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace phpbbgallery\core;

class upload
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
	//private $tools = null;

	/**
	* Basic variables...
	*/
	public $loaded_files = 0;
	public $uploaded_files = 0;
	public $errors = array();
	public $images = array();
	public $image_data = array();
	public $array_id2row = array();
	public $error_prefix = '';
	public $max_filesize = 0;
	private $file_limit = 0;
	private $album_id = 0;
	private $file_count = 0;
	private $image_num = 0;
	private $allow_comments = false;
	private $sent_quota_error = false;
	private $username = '';
	private $file_descriptions = array();
	private $file_names = array();
	private $file_rotating = array();

	var $min_width = 0;
	var $min_height = 0;
	var $max_width = 0;
	var $max_height = 0;

	/**
	 * Constructor
	 *
	 * @param \phpbb\user $user phpBB User class
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\event\dispatcher_interface $phpbb_dispatcher
	 * @param \phpbb\request\request $request
	 * @param \phpbbgallery\core\image\image $gallery_image
	 * @param \phpbbgallery\core\config $gallery_config Gallery Config
	 * @param \phpbbgallery\core\url $gallery_url Gallery url
	 * @param block $block
	 * @param file\file $gallery_file
	 * @param                                   $images_table
	 * @param                                   $root_path
	 * @param                                   $php_ext
	 */
	public function __construct(\phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\event\dispatcher_interface $phpbb_dispatcher, \phpbb\request\request $request,
								\phpbbgallery\core\image\image $gallery_image, \phpbbgallery\core\config $gallery_config, \phpbbgallery\core\url $gallery_url, \phpbbgallery\core\block $block,
								\phpbbgallery\core\file\file $gallery_file,
								$images_table,
								$root_path, $php_ext)
	{
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_dispatcher = $phpbb_dispatcher;
		$this->request = $request;
		$this->gallery_image = $gallery_image;
		$this->gallery_config = $gallery_config;
		$this->gallery_url	= $gallery_url;
		$this->block = $block;
		$this->tools = $gallery_file;
		$this->images_table = $images_table;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * As we have to use construct for setting up infrastructure the right way,
	 * we'll be creating this setup function that should setup everything.
	 * @param     $album_id 	Album ID we are uploading to
	 * @param int $num_files	Number of files we upload
	 */
	public function set_up($album_id, $num_files = 0)
	{
		if (!class_exists('fileupload'))
		{
			include_once($this->root_path . 'includes/functions_upload.' . $this->php_ext);
		}
		$this->upload = new \fileupload();
		$this->upload->fileupload('', $this->get_allowed_types(), (4 * $this->gallery_config->get('max_filesize')));

		$this->album_id = (int) $album_id;
		$this->file_limit = (int) $num_files;
		$this->username = $this->user->data['username'];

		$this->max_filesize = 4 * $this->gallery_config->get('max_filesize');
	}

	/**
	 * Upload a file and then call the function for reading the zip or preparing the image
	 *
	 * @param $file_count
	 * @return bool
	 */
	public function upload_file($file_count)
	{
		if ($this->file_limit && ($this->uploaded_files >= $this->file_limit))
		{
			$this->quota_error();
			return false;
		}
		$this->file_count = (int) $file_count;

		$this->files = $this->form_upload('files');
		foreach ($this->files as $var)
		{
			$this->file = $var;
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
	}

	/**
	* Upload a zip file and save the images into the import/ directory.
	*/
	public function upload_zip()
	{
		if (!class_exists('compress_zip'))
		{
			include_once($this->root_path . 'includes/functions_compress.' . $this->php_ext);
		}

		$tmp_dir = $this->gallery_url->path('import') . 'tmp_' . md5(unique_id()) . '/';

		$this->zip_file->clean_filename('unique_ext');
		$this->zip_file->move_file(substr($this->gallery_url->path('import_noroot'), 0, -1), false, false, CHMOD_ALL);
		if (!empty($this->zip_file->error))
		{
			$this->zip_file->remove();
			$this->new_error($this->user->lang('UPLOAD_ERROR', $this->zip_file->uploadname, implode('<br />&raquo; ', $this->zip_file->error)));
			return false;
		}

		$compress = new \compress_zip('r', $this->zip_file->destination_file);
		$compress->extract($tmp_dir);
		$compress->close();

		$this->zip_file->remove();

		// Remove zip from allowed extensions
		$this->upload->set_allowed_extensions($this->get_allowed_types(false, true));

		$this->read_zip_folder($tmp_dir);

		// Read zip from allowed extensions
		$this->upload->set_allowed_extensions($this->get_allowed_types());
	}

	/**
	 * Read a folder from the zip, "upload" the images and remove the rest.
	 *
	 * @param $current_dir
	 */
	public function read_zip_folder($current_dir)
	{
		$handle = opendir($current_dir);
		while ($file = readdir($handle))
		{
			if ($file == '.' || $file == '..')
			{
				continue;
			}
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
						$this->new_error($this->user->lang('UPLOAD_ERROR', $this->file->uploadname, implode('<br />&raquo; ', $this->file->error)));
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
							$this->new_error($this->user->lang('UPLOAD_ERROR', $this->file->uploadname, implode('<br />&raquo; ', $this->file->error)));
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
	 *
	 * @param int $image_id
	 * @param bool $needs_approval
	 * @param bool $is_in_contest
	 * @return bool
	 */
	public function update_image($image_id, $needs_approval = false, $is_in_contest = false)
	{
		if ($this->file_limit && ($this->uploaded_files >= $this->file_limit))
		{
			$this->new_error($this->user->lang('UPLOAD_ERROR', $this->image_data[$image_id]['image_name'], $this->user->lang['QUOTA_REACHED']));
			return false;
		}
		$this->file_count = (int) $this->array_id2row[$image_id];

		// Create message parser instance
		if (!class_exists('parse_message'))
		{
			include_once($this->root_path . 'includes/message_parser.' . $this->php_ext);
		}
		$message_parser = new \parse_message();
		$message_parser->message	= utf8_normalize_nfc($this->get_description());
		if ($message_parser->message)
		{
			$message_parser->parse(true, true, true, true, false, true, true, true);
		}

		$sql_ary = array(
			'image_status'				=> ($needs_approval) ? $this->block->get_image_status_unapproved() : $this->block->get_image_status_approved(),
			'image_contest'				=> ($is_in_contest) ? $this->block->get_in_contest() : $this->block->get_no_contest(),
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

		$additional_sql_data = array();
		$image_data = $this->image_data[$image_id];
		$file_link = $this->gallery_url->path('upload') . $this->image_data[$image_id]['image_filename'];

		/**
		* Event upload image before
		*
		* @event phpbbgallery.core.upload.update_image_before
		* @var	array	additional_sql_data		array of additional settings
		* @var	array	image_data				array of image_data
		* @var	string	file_link				link to file
		* @since 1.2.0
		*/
		$vars = array('additional_sql_data', 'image_data', 'file_link');
		extract($this->phpbb_dispatcher->trigger_event('phpbbgallery.core.upload.update_image_before', compact($vars)));

		// Rotate image
		if (!$this->prepare_file_update($image_id))
		{
			/**
			* Event upload image update
			*
			* @event phpbbgallery.core.upload.update_image_nofilechange
			* @var	array	additional_sql_data		array of additional settings
			* @since 1.2.0
			*/
			$vars = array('additional_sql_data');
			extract($this->phpbb_dispatcher->trigger_event('phpbbgallery.core.upload.update_image_nofilechange', compact($vars)));
		}

		$sql_ary = array_merge($sql_ary, $additional_sql_data);

		$sql = 'UPDATE ' . $this->images_table. ' 
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE image_id = ' . (int) $image_id;
		$this->db->sql_query($sql);

		$this->uploaded_files++;

		return true;
	}

	/**
	* Prepare file on upload: rotate and resize
	*/
	public function prepare_file()
	{
		$upload_dir = $this->get_current_upload_dir();

		// Rename the file, move it to the correct location and set chmod
		if (!$upload_dir)
		{
			$this->file->clean_filename('unique_ext');
			$this->file->move_file(substr($this->gallery_url->path('upload'), 0, -1), false, false, CHMOD_ALL);
		}
		else
		{
			// Okay, this looks hacky, but what we do here is, we store the directory name in the filename.
			// However phpBB strips directories form the filename, when moving, so we need to specify that again.
			$this->file->clean_filename('unique_ext', $upload_dir . '/');
			$this->file->move_file($this->gallery_url->path('upload_noroot') . $upload_dir, false, false, CHMOD_ALL);
		}

		if (!empty($this->file->error))
		{
			$this->file->remove();
			$this->new_error($this->user->lang('UPLOAD_ERROR', $this->file->uploadname, implode('<br />&raquo; ', $this->file->error)));
			return false;
		}
		@chmod($this->file->destination_file, 0777);
		$additional_sql_data = array();
		$file = $this->file;

		/**
		* Event upload image update
		*
		* @event phpbbgallery.core.upload.prepare_file_before
		* @var	array	additional_sql_data		array of additional settings
		* @var	array	file					File object
		* @since 1.2.0
		*/
		$vars = array('additional_sql_data', 'file');
		extract($this->phpbb_dispatcher->trigger_event('phpbbgallery.core.upload.prepare_file_before', compact($vars)));

		$this->tools->set_image_options($this->max_filesize, $this->gallery_config->get('max_height'), $this->gallery_config->get('max_width'));
		$this->tools->set_image_data($this->file->destination_file, '', $this->file->filesize, true);

		// Rotate the image
		if ($this->gallery_config->get('allow_rotate') && $this->get_rotating())
		{
			$this->tools->rotate_image($this->get_rotating(), $this->gallery_config->get('allow_resize'));
			if ($this->tools->rotated)
			{
				$this->file->height = $this->tools->image_size['height'];
				$this->file->width = $this->tools->image_size['width'];
			}
		}

		// Resize oversized images
		if (($this->file->width > $this->gallery_config->get('max_width')) || ($this->file->height > $this->gallery_config->get('max_height')))
		{
			if ($this->gallery_config->get('allow_resize'))
			{
				$this->tools->resize_image($this->gallery_config->get('max_width'), $this->gallery_config->get('max_height'));
				if ($this->tools->resized)
				{
					$this->file->height = $this->tools->image_size['height'];
					$this->file->width = $this->tools->image_size['width'];
				}
			}
			else
			{
				$this->file->remove();
				$this->new_error($this->user->lang('UPLOAD_ERROR', $this->file->uploadname, $this->user->lang['UPLOAD_IMAGE_SIZE_TOO_BIG']));
				return false;
			}
		}

		if ($this->file->filesize > (1.2 * $this->max_filesize))
		{
			$this->file->remove();
			$this->new_error($this->user->lang('UPLOAD_ERROR', $this->file->uploadname, $this->user->lang['BAD_UPLOAD_FILE_SIZE']));
			return false;
		}

		if ($this->tools->rotated || $this->tools->resized)
		{
			$this->tools->write_image($this->file->destination_file, $this->gallery_config->get('jpg_quality'), true);
		}

		// Everything okay, now add the file to the database and return the image_id

		return $this->file_to_database($additional_sql_data);
	}

	/**
	 * Prepare file on second upload step.
	 * You can still rotate the image there.
	 *
	 * @param $image_id
	 * @return mixed
	 */
	public function prepare_file_update($image_id)
	{
		$this->tools->set_image_options($this->max_filesize, $this->gallery_config->get('max_height'), $this->gallery_config->get('max_width'));
		$this->tools->set_image_data($this->gallery_url->path('upload') . $this->image_data[$image_id]['image_filename'], '', 0, true);

		// Rotate the image
		if ($this->gallery_config->get('allow_rotate') && $this->get_rotating())
		{
			$this->tools->rotate_image($this->get_rotating(),$this->gallery_config->get('allow_resize'));
			if ($this->tools->rotated)
			{
				$this->tools->write_image($this->tools->image_source, $this->gallery_config->get('jpg_quality'), true);
				@unlink($this->gallery_url->path('thumbnail') . $this->image_data[$image_id]['image_filename']);
				@unlink($this->gallery_url->path('medium') . $this->image_data[$image_id]['image_filename']);
			}
		}
		return $this->tools->rotated;
	}

	/**
	 * Insert the file into the database
	 *
	 * @param $additional_sql_ary
	 * @return int
	 */
	public function file_to_database($additional_sql_ary)
	{
		$image_name = str_replace("_", "_", utf8_substr($this->file->uploadname, 0, utf8_strrpos($this->file->uploadname, '.')));

		$sql_ary = array_merge(array(
			'image_name'			=> $image_name,
			'image_name_clean'		=> utf8_clean_string($image_name),
			'image_filename' 		=> $this->file->realname,
			'filesize_upload'		=> $this->file->filesize,
			'image_time'			=> time() + $this->file_count,

			'image_user_id'			=> $this->user->data['user_id'],
			'image_user_colour'		=> $this->user->data['user_colour'],
			'image_username'		=> $this->username,
			'image_username_clean'	=> utf8_clean_string($this->username),
			'image_user_ip'			=> $this->user->ip,

			'image_album_id'		=> $this->album_id,
			'image_status'			=> $this->block->get_image_status_orphan(),
			'image_contest'			=> $this->block->get_no_contest(),
			'image_allow_comments'	=> $this->allow_comments,
			'image_desc'			=> '',
			'image_desc_uid'		=> '',
			'image_desc_bitfield'	=> '',
		), $additional_sql_ary);

		$sql = 'INSERT INTO ' . $this->images_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);

		$image_id = (int) $this->db->sql_nextid();
		$this->image_data[$image_id] = $sql_ary;

		return $image_id;
	}

	/**
	 * Delete orphan uploaded files, which are older than half an hour...
	 *
	 * @param int $time
	 */
	public function prune_orphan($time = 0)
	{
		$prunetime = (int) (($time) ? $time : (time() - 1800));

		$sql = 'SELECT image_id, image_filename
			FROM ' . $this->images_table . '
			WHERE image_status = ' . $this->block->get_image_status_orphan() . '
				AND image_time < ' . $prunetime;
		$result = $this->db->sql_query($sql);
		$images = $filenames = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$images[] = (int) $row['image_id'];
			$filenames[(int) $row['image_id']] = $row['image_filename'];
		}
		$this->db->sql_freeresult($result);

		if ($images)
		{
			$this->gallery_image->delete_images($images, $filenames, false);
		}
	}

	/**
	 * Get the current upload dir (doh!)
	 * @return int|mixed|string
	 */
	private function get_current_upload_dir()
	{
		if (self::NUM_FILES_PER_DIR <= 0)
		{
			return 0;
		}

		// This code is never invoced. It's left here for future implementation.
		$this->gallery_config->inc('current_upload_dir_size', 1);
		if ($this->gallery_config->get('current_upload_dir_size') >= self::NUM_FILES_PER_DIR)
		{
			$this->gallery_config->set('current_upload_dir_size', 0);
			$this->gallery_config->inc('current_upload_dir', 1);
			@mkdir($this->gallery_url->path('upload') . $this->gallery_config->get('current_upload_dir'));
			@mkdir($this->gallery_url->path('medium') . $this->gallery_config->get('current_upload_dir'));
			@mkdir($this->gallery_url->path('thumbnail') . $this->gallery_config->get('current_upload_dir'));
			@copy($this->gallery_url->path('upload') . 'index.htm', $this->gallery_url->path('upload') . $this->gallery_config->get('current_upload_dir') . '/index.htm');
			@copy($this->gallery_url->path('upload') . 'index.htm', $this->gallery_url->path('medium') . $this->gallery_config->get('current_upload_dir') . '/index.htm');
			@copy($this->gallery_url->path('upload') . 'index.htm', $this->gallery_url->path('thumbnail') . $this->gallery_config->get('current_upload_dir') . '/index.htm');
			@copy($this->gallery_url->path('upload') . '.htaccess', $this->gallery_url->path('upload') . $this->gallery_config->get('current_upload_dir') . '/.htaccess');
			@copy($this->gallery_url->path('upload') . '.htaccess', $this->gallery_url->path('medium') . $this->gallery_config->get('current_upload_dir') . '/.htaccess');
			@copy($this->gallery_url->path('upload') . '.htaccess', $this->gallery_url->path('thumbnail') . $this->gallery_config->get('current_upload_dir') . '/.htaccess');
		}
		return $this->gallery_config->get('current_upload_dir');
	}

	public function quota_error()
	{
		if ($this->sent_quota_error)
		{
			return;
		}
		$this->new_error($this->user->lang('USER_REACHED_QUOTA_SHORT', $this->file_limit));
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
		$image_ids = $filenames = array();
		foreach ($uploaded_ids as $row => $check)
		{
			if (strpos($check, '$') == false)
			{
				continue;
			}
			list($image_id, $filename) = explode('$', $check);
			$image_ids[] = (int) $image_id;
			$filenames[$image_id] = $filename;
			$this->array_id2row[$image_id] = $row;
		}

		if (empty($image_ids))
		{
			return;
		}

		$sql = 'SELECT *
			FROM ' . $this->images_table . '
			WHERE image_status = ' . $this->block->get_image_status_orphan() . '
				AND ' . $this->db->sql_in_set('image_id', $image_ids);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($filenames[$row['image_id']] == substr($row['image_filename'], 0, 8))
			{
				$this->images[] = (int) $row['image_id'];
				$this->image_data[(int) $row['image_id']] = $row;
				$this->loaded_files++;
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Get an array of allowed file types or file extensions
	 *
	 * @param bool $get_types
	 * @param bool $ignore_zip
	 * @return array
	 */
	public function get_allowed_types($get_types = false, $ignore_zip = false)
	{
		$extensions = $types = array();
		if ($this->gallery_config->get('allow_jpg'))
		{
			$types[] = $this->user->lang['FILETYPES_JPG'];
			$extensions[] = 'jpg';
			$extensions[] = 'jpeg';
		}
		if ($this->gallery_config->get('allow_gif'))
		{
			$types[] = $this->user->lang['FILETYPES_GIF'];
			$extensions[] = 'gif';
		}
		if ($this->gallery_config->get('allow_png'))
		{
			$types[] = $this->user->lang['FILETYPES_PNG'];
			$extensions[] = 'png';
		}
		if (!$ignore_zip && $this->gallery_config->get('allow_zip'))
		{
			$types[] = $this->user->lang['FILETYPES_ZIP'];
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

	/**
	 * Here we do a lot of hacking and slashing ... don't ask why ... I will have to change it to plupload usage once I know how!
	 */

	/**
	 * Form upload method
	 * Upload file from users harddisk
	 *
	 * @param string $form_name Form name assigned to the file input field (if it is an array, the key has to be specified)
	 * @param \phpbb\mimetype\guesser $mimetype_guesser Mimetype guesser
	 * @return object $file Object "filespec" is returned, all further operations can be done with this object
	 * @internal param \phpbb\plupload\plupload $plupload The plupload object
	 *
	 * @access public
	 */
	private function form_upload($form_name, \phpbb\mimetype\guesser $mimetype_guesser = null)
	{
		$upload_unstr = $this->request->variable('files', array('name'=> array('' => ''), 'type' => array('' => ''), 'tmp_name' => array('' => ''), 'error' =>  array('' => ''), 'size' => array('' => '')), true, \phpbb\request\request_interface::FILES);

		$upload_redy = array();
		for ($i = 0; $i < count($upload_unstr['name']); $i++)
		{
			$upload_redy[$i] = array(
				'name' => $upload_unstr['name'][$i],
				'type' => $upload_unstr['type'][$i],
				'tmp_name' => $upload_unstr['tmp_name'][$i],
				'error'	=> ($upload_unstr['error'][$i] ? $upload_unstr['error'][$i] : null),
				'size'	=> $upload_unstr['size'][$i]
			);
		}

		foreach ($upload_redy as $ID => $var)
		{
			$upload = array(
				'name' => $var['name'],
				'type' => $var['type'],
				'tmp_name' => $var['tmp_name'],
				'error'	=> $var['error'],
				'size'	=> $var['size']
			);
			$file = new \filespec($upload, $this, $mimetype_guesser, null);
			if ($file->init_error)
			{
				$file->error[] = '';
				$files[$ID] = $file;
				continue;
			}
			// Error array filled?
			if (isset($upload['error']))
			{
				$error = $upload['error'];
				if ($error !== false)
				{
					$file->error[] = $error;
					$files[$ID] = $file;
					continue;
				}
			}
			// Check if empty file got uploaded (not catched by is_uploaded_file)
			if (isset($upload['size']) && $upload['size'] == 0)
			{
				$file->error[] = $this->user->lang['FILE_EMPTY_FILEUPLOAD'];
				$files[$ID] = $file;
				continue;
			}
			// PHP Upload filesize exceeded
			if ($file->get('filename') == 'none')
			{
				$max_filesize = @ini_get('upload_max_filesize');
				$unit = 'MB';
				if (!empty($max_filesize))
				{
					$unit = strtolower(substr($max_filesize, -1, 1));
					$max_filesize = (int) $max_filesize;
					$unit = ($unit == 'k') ? 'KB' : (($unit == 'g') ? 'GB' : 'MB');
				}
				$file->error[] = (empty($max_filesize)) ? $this->user->lang['FILE_PHP_SIZE_NA'] : sprintf($this->user->lang['FILE_PHP_SIZE_OVERRUN'], $max_filesize, $this->user->lang[$unit]);
				$files[$ID] = $file;
				continue;
			}

			// Not correctly uploaded
			if (!$file->is_uploaded())
			{
				$file->error[] = $this->user->lang['FILE_NOT_UPLOADED'];
				$files[$ID] = $file;
				continue;
			}
			$this->common_checks($file);
			$files[$ID] = $file;
			continue;
		}
		return $files;
	}

	/**
	 * Perform common checks
	 * @param $file
	 */
	function common_checks(&$file)
	{
		// Filesize is too big or it's 0 if it was larger than the maxsize in the upload form
		if ($this->max_filesize && ($file->get('filesize') > $this->max_filesize || $file->get('filesize') == 0))
		{
			$max_filesize = get_formatted_filesize($this->max_filesize, false);
			$file->error[] = sprintf($this->user->lang['FILE_WRONG_FILESIZE'], $max_filesize['value'], $max_filesize['unit']);
		}
		// check Filename
		if (preg_match("#[\\/:*?\"<>|]#i", $file->get('realname')))
		{
			$file->error[] = sprintf($this->user->lang['FILE_INVALID_FILENAME'], $file->get('realname'));
		}
		// Invalid Extension
		if (!$this->valid_extension($file))
		{
			$file->error[] = sprintf($this->user->lang['FILE_DISALLOWED_EXTENSION'], $file->get('extension'));
		}
		// MIME Sniffing
		//if (!$this->valid_content($file))
		//{
		//	$file->error[] = sprintf($this->user->lang['FILE_DISALLOWED_CONTENT']);
		//}
	}

	function valid_extension($file)
	{
		$allowed = array();
		if ($this->gallery_config->get('allow_jpg'))
		{
			$allowed[] = 'jpg';
			$allowed[] = 'jpeg';
		}
		if ($this->gallery_config->get('allow_gif'))
		{
			$allowed[] = 'gif';
		}
		if ($this->gallery_config->get('allow_png'))
		{
			$allowed[] = 'png';
		}
		if ($this->gallery_config->get('allow_zip'))
		{
			$allowed[] = 'zip';
		}
		return in_array($file->get('extension'), $allowed);
	}

	/**
	 * Check for allowed dimension
	 * @param $file
	 * @return bool
	 */
	function valid_dimensions(&$file)
	{
		if (!$this->max_width && !$this->max_height && !$this->min_width && !$this->min_height)
		{
			return true;
		}
		if (($file->get('width') > $this->max_width && $this->max_width) ||
			($file->get('height') > $this->max_height && $this->max_height) ||
			($file->get('width') < $this->min_width && $this->min_width) ||
			($file->get('height') < $this->min_height && $this->min_height))
		{
			return false;
		}
		return true;
	}
}
