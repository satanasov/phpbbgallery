<?php
/**
*
* @package Gallery - ACP Import Extension
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\acpimport\acp;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $auth, $cache, $config, $db, $template, $user, $phpEx, $phpbb_root_path, $phpbb_container, $gallery_url, $gallery_config, $gallery_album;

		$gallery_url = $phpbb_container->get('phpbbgallery.core.url');
		$gallery_config = $phpbb_container->get('phpbbgallery.core.config');
		$gallery_album = $phpbb_container->get('phpbbgallery.core.album');
		$gallery_url->_include('functions_display', 'phpbb');

		$user->add_lang_ext('phpbbgallery/core', array('gallery_acp', 'gallery'));
		$this->tpl_name = 'gallery_acpimport';
		add_form_key('acp_gallery');

		$this->page_title = $user->lang['ACP_IMPORT_ALBUMS'];
		$this->import();
	}

	function import()
	{
		global $db, $template, $user, $phpbb_dispatcher, $phpbb_container, $gallery_url, $request, $table_prefix ,$gallery_config, $gallery_album, $request;

		$import_schema = $request->variable('import_schema', '');
		$images = $request->variable('images', array(''), true);

		$submit = (isset($_POST['submit'])) ? true : ((empty($images)) ? false : true);

		if ($import_schema)
		{
			if ($gallery_url->_file_exists($import_schema, 'import', ''))
			{
				include($gallery_url->_return_file($import_schema, 'import', ''));
				// Replace the md5 with the ' again and remove the space at the end to prevent \' troubles
				$user_data['username'] = utf8_substr(str_replace("{{$import_schema}}", "'", $user_data['username']), 0, -1);
				$image_name = utf8_substr(str_replace("{{$import_schema}}", "'", $image_name), 0, -1);
			}
			else
			{
				global $phpEx;
				trigger_error($user->lang('MISSING_IMPORT_SCHEMA', ($import_schema . '.' . $phpEx)), E_USER_WARNING);
			}

			$images_loop = 0;
			foreach ($images as $image_src)
			{
				/**
				* Import the images
				*/

				$image_src = str_replace("{{$import_schema}}", "'", $image_src);
				$image_src_full = $gallery_url->path('import') . utf8_decode($image_src);
				if (file_exists($image_src_full))
				{
					$filetype = getimagesize($image_src_full);
					$filetype_ext = '';

					$error_occured = false;
					switch ($filetype['mime'])
					{
						case 'image/jpeg':
						case 'image/jpg':
						case 'image/pjpeg':
							$filetype_ext = '.jpg';
							$read_function = 'imagecreatefromjpeg';
							if ((substr(strtolower($image_src), -4) != '.jpg') && (substr(strtolower($image_src), -5) != '.jpeg'))
							{
								$this->log_import_error($import_schema, sprintf($user->lang['FILETYPE_MIMETYPE_MISMATCH'], $image_src, $filetype['mime']));
								$error_occured = true;
							}
						break;

						case 'image/png':
						case 'image/x-png':
							$filetype_ext = '.png';
							$read_function = 'imagecreatefrompng';
							if (substr(strtolower($image_src), -4) != '.png')
							{
								$this->log_import_error($import_schema, sprintf($user->lang['FILETYPE_MIMETYPE_MISMATCH'], $image_src, $filetype['mime']));
								$error_occured = true;
							}
						break;

						case 'image/gif':
						case 'image/giff':
							$filetype_ext = '.gif';
							$read_function = 'imagecreatefromgif';
							if (substr(strtolower($image_src), -4) != '.gif')
							{
								$this->log_import_error($import_schema, sprintf($user->lang['FILETYPE_MIMETYPE_MISMATCH'], $image_src, $filetype['mime']));
								$error_occured = true;
							}
						break;

						default:
							$this->log_import_error($import_schema, $user->lang['NOT_ALLOWED_FILE_TYPE']);
							$error_occured = true;
						break;
					}
					$image_filename = md5(unique_id()) . $filetype_ext;

					if (!$error_occured || !@move_uploaded_file($image_src_full, $gallery_url->path('upload') . $image_filename))
					{
						if (!@copy($image_src_full, $gallery_url->path('upload') . $image_filename))
						{
							$user->add_lang('posting');
							$this->log_import_error($import_schema, sprintf($user->lang['GENERAL_UPLOAD_ERROR'], $gallery_url->path('upload') . $image_filename));
							$error_occured = true;
						}
					}

					if (!$error_occured)
					{
						@chmod($gallery_url->path('upload') . $image_filename, 0777);
						// The source image is imported, so we delete it.
						@unlink($image_src_full);

						$sql_ary = array(
							'image_filename' 		=> $image_filename,
							'image_desc'			=> '',
							'image_desc_uid'		=> '',
							'image_desc_bitfield'	=> '',
							'image_user_id'			=> $user_data['user_id'],
							'image_username'		=> $user_data['username'],
							'image_username_clean'	=> utf8_clean_string($user_data['username']),
							'image_user_colour'		=> $user_data['user_colour'],
							'image_user_ip'			=> $user->ip,
							'image_time'			=> $start_time + $done_images,
							'image_album_id'		=> $album_id,
							'image_status'			=> \phpbbgallery\core\block::STATUS_APPROVED,
							//'image_exif_data'		=> '',
						);

						$image_tools = $phpbb_container->get('phpbbgallery.core.file.tool');
						$image_tools->set_image_options($gallery_config->get('max_filesize'), $gallery_config->get('max_height'), $gallery_config->get('max_width'));
						$image_tools->set_image_data($gallery_url->path('upload') . $image_filename);

						$additional_sql_data = array();
						$file_link = $gallery_url->path('upload') . $image_filename;

						/**
						* Event to trigger before mass update
						*
						* @event phpbbgallery.acpimport.update_image_before
						* @var	array	additional_sql_data		array of additional sql_data
						* @var	string	file_link				String with real file link
						* @since 1.2.0
						*/
						$vars = array('additional_sql_data', 'file_link');
						extract($phpbb_dispatcher->trigger_event('phpbbgallery.acpimport.update_image_before', compact($vars)));

						if (($filetype[0] > $gallery_config->get('max_width')) || ($filetype[1] > $gallery_config->get('max_height')))
						{
							/**
							* Resize overside images
							*/
							if ($gallery_config->get('allow_resize'))
							{
								$image_tools->resize_image($gallery_config->get('max_width'), $gallery_config->get('max_height'));
								if ($image_tools->resized)
								{
									$image_tools->write_image($gallery_url->path('upload') . $image_filename, $gallery_config->get('jpg_quality'), true);
								}
							}
						}
						$file_updated = (bool) $image_tools->resized;

						/**
						* Event to trigger before mass update
						*
						* @event phpbbgallery.acpimport.update_image
						* @var	array	additional_sql_data		array of additional sql_data
						* @var	bool	file_updated			is file resized
						* @since 1.2.0
						*/
						$vars = array('additional_sql_data', 'file_updated');
						extract($phpbb_dispatcher->trigger_event('phpbbgallery.acpimport.update_image', compact($vars)));

						$sql_ary = array_merge($sql_ary, $additional_sql_data);

						// Try to get real filesize from temporary folder (not always working) ;)
						$sql_ary['filesize_upload'] = (@filesize($gallery_url->path('upload') . $image_filename)) ? @filesize($gallery_url->path('upload') . $image_filename) : 0;

						if ($filename || ($image_name == ''))
						{
							$sql_ary['image_name'] = str_replace("_", " ", utf8_substr($image_src, 0, utf8_strrpos($image_src, '.')));
						}
						else
						{
							$sql_ary['image_name'] = str_replace('{NUM}', $num_offset + $done_images, $image_name);
						}
						$sql_ary['image_name_clean'] = utf8_clean_string($sql_ary['image_name']);

						// Put the images into the database
						$db->sql_query('INSERT INTO ' . $table_prefix . 'gallery_images ' . $db->sql_build_array('INSERT', $sql_ary));
					}
					$done_images++;
				}

				// Remove the image from the list
				unset($images[$images_loop]);
				$images_loop++;
				if ($images_loop == 10)
				{
					// We made 10 images, so we end for this turn
					break;
				}
			}
			if ($images_loop)
			{
				$image_user = $phpbb_container->get('phpbbgallery.core.user');
				$image_user->set_user_id($user_data['user_id']);
				$image_user->update_images($images_loop);

				$gallery_config->inc('num_images', $images_loop);
				$todo_images = $todo_images - $images_loop;
			}
			$gallery_album->update_info($album_id);

			if (!$todo_images)
			{
				unlink($gallery_url->_return_file($import_schema, 'import', ''));
				$errors = @file_get_contents($gallery_url->_return_file($import_schema . '_errors', 'import', ''));
				@unlink($gallery_url->_return_file($import_schema . '_errors', 'import', ''));
				if (!$errors)
				{
					trigger_error(sprintf($user->lang['IMPORT_FINISHED'], $done_images) . adm_back_link($this->u_action));
				}
				else
				{
					$errors = explode("\n", $errors);
					trigger_error(sprintf($user->lang['IMPORT_FINISHED_ERRORS'], $done_images - sizeof($errors)) . implode('<br />', $errors) . adm_back_link($this->u_action), E_USER_WARNING);
				}
			}
			else
			{
				// Write the new list
				$this->create_import_schema($import_schema, $album_id, $user_data, $start_time, $num_offset, $done_images, $todo_images, $image_name, $filename, $images);

				// Redirect
				$forward_url = $this->u_action . "&amp;import_schema=$import_schema";
				meta_refresh(1, $forward_url);
				trigger_error(sprintf($user->lang['IMPORT_DEBUG_MES'], $done_images, $todo_images));
			}
		}
		else if ($submit)
		{
			if (!check_form_key('acp_gallery'))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}
			if (!$images)
			{
				trigger_error('NO_FILE_SELECTED', E_USER_WARNING);
			}

			// Who is the uploader?
			$username = $request->variable('username', '', true);
			$user_id = 0;
			if ($username)
			{
				if (!function_exists('user_get_id_name'))
				{
					$gallery_url->_include('functions_user', 'phpbb');
				}
				user_get_id_name($user_id, $username);
			}
			if (is_array($user_id))
			{
				$user_id = $user_id[0];
			}
			if (!$user_id)
			{
				$user_id = $user->data['user_id'];
			}

			$sql = 'SELECT username, user_colour, user_id
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . $user_id;
			$result = $db->sql_query($sql);
			$user_row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			if (!$user_row)
			{
				trigger_error('HACKING_ATTEMPT', E_USER_WARNING);
			}

			$album_id = $request->variable('album_id', 0);
			if (isset($_POST['users_pega']))
			{
				$image_user =  $phpbb_container->get('phpbbgallery.core.user');
				$image_user->set_user_id($user_row['user_id']);
				if ($user->data['user_id'] != $user_row['user_id'])
				{
					$album_id = $image_user->get_data('personal_album_id');
					if (!$album_id)
					{
						// The User has no personal album
						$album_id = $gallery_album->generate_personal_album($user_row['username'], $user_row['user_id'], $user_row['user_colour'], $image_user);
					}
					unset($image_user);
				}
				else
				{
					$album_id = $image_user->get_data('personal_album_id');
					if (!$album_id)
					{
						$album_id = $gallery_album->generate_personal_album($user_row['username'], $user_row['user_id'], $user_row['user_colour'], $image_user);
					}
				}
			}

			// Where do we put them to?
			$sql = 'SELECT album_id, album_name
				FROM ' . $table_prefix . 'gallery_albums
				WHERE album_id = ' . $album_id;
			$result = $db->sql_query($sql);
			$album_row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			if (!$album_row)
			{
				trigger_error('HACKING_ATTEMPT', E_USER_WARNING);
			}

			$start_time = time();
			$import_schema = md5($start_time);
			$filename = ($request->variable('filename', '') == 'filename') ? true : false;
			$image_name = $request->variable('image_name', '', true);
			$num_offset = $request->variable('image_num', 0);

			$this->create_import_schema($import_schema, $album_row['album_id'], $user_row, $start_time, $num_offset, 0, sizeof($images), $image_name, $filename, $images);

			$forward_url = $this->u_action . "&amp;import_schema=$import_schema";
			meta_refresh(2, $forward_url);
			trigger_error('IMPORT_SCHEMA_CREATED');
		}

		$handle = opendir($gallery_url->path('import'));
		$files = array();
		while ($file = readdir($handle))
		{
			if (!is_dir($gallery_url->path('import') . $file) && (
			((substr(strtolower($file), -4) == '.png') && $gallery_config->get('allow_png')) ||
			((substr(strtolower($file), -4) == '.gif') && $gallery_config->get('allow_gif')) ||
			((substr(strtolower($file), -4) == '.jpg') && $gallery_config->get('allow_jpg')) ||
			((substr(strtolower($file), -5) == '.jpeg') && $gallery_config->get('allow_jpg'))
			))
			{
				$files[utf8_strtolower($file)] = $file;
			}
		}
		closedir($handle);

		// Sort the files by name again
		ksort($files);
		foreach ($files as $file)
		{
			$template->assign_block_vars('imagerow', array(
				'FILE_NAME'				=> utf8_encode($file),
			));
		}

		$template->assign_vars(array(
			'S_IMPORT_IMAGES'				=> true,
			'ACP_GALLERY_TITLE'				=> $user->lang['ACP_IMPORT_ALBUMS'],
			'ACP_GALLERY_TITLE_EXPLAIN'		=> $user->lang['ACP_IMPORT_ALBUMS_EXPLAIN'],
			'L_IMPORT_DIR_EMPTY'			=> sprintf($user->lang['IMPORT_DIR_EMPTY'], $gallery_url->path('import')),
			'S_ALBUM_IMPORT_ACTION'			=> $this->u_action,
			'S_SELECT_IMPORT' 				=> $gallery_album->get_albumbox(false, 'album_id', false, false, false, \phpbbgallery\core\block::PUBLIC_ALBUM, \phpbbgallery\core\block::TYPE_UPLOAD),
			'U_FIND_USERNAME'				=> $gallery_url->append_sid('phpbb', 'memberlist', 'mode=searchuser&amp;form=acp_gallery&amp;field=username&amp;select_single=true'),
		));
	}

	function create_import_schema($import_schema, $album_id, $user_row, $start_time, $num_offset, $done_images, $todo_images, $image_name, $filename, $images)
	{
		global $gallery_url;

		$import_file = "<?php\n\nif (!defined('IN_PHPBB'))\n{\n	exit;\n}\n\n";
		$import_file .= "\$album_id = " . $album_id . ";\n";
		$import_file .= "\$start_time = " . $start_time . ";\n";
		$import_file .= "\$num_offset = " . $num_offset . ";\n";
		$import_file .= "\$done_images = " . $done_images . ";\n";
		$import_file .= "\$todo_images = " . $todo_images . ";\n";
		// We add a space at the end of the name, to not get troubles with \';
		$import_file .= "\$image_name = '" . str_replace("'", "{{$import_schema}}", $image_name) . " ';\n";
		$import_file .= "\$filename = " . (($filename) ? 'true' : 'false') . ";\n";
		$import_file .= "\$user_data = array(\n";
		$import_file .= "	'user_id'		=> " . $user_row['user_id'] . ",\n";
		// We add a space at the end of the name, to not get troubles with \',
		$import_file .= "	'username'		=> '" . str_replace("'", "{{$import_schema}}", $user_row['username']) . " ',\n";
		$import_file .= "	'user_colour'	=> '" . $user_row['user_colour'] . "',\n";
		$import_file .= ");\n";
		$import_file .= "\$images = array(\n";

		// We need to replace some characters to find the image and not produce syntax errors
		$replace_chars = array("'", "&amp;");
		$replace_with = array("{{$import_schema}}", "&");

		foreach ($images as $image_src)
		{
			$import_file .= "	'" . str_replace($replace_chars, $replace_with, $image_src) . "',\n";
		}
		$import_file .= ");\n\n?" . '>'; // Done this to prevent highlighting editors getting confused!

		// Write to disc
		if (($gallery_url->_file_exists($import_schema, 'import', '') && $gallery_url->_is_writable($import_schema, 'import', '')) || $gallery_url->_is_writable('', 'import', ''))
		{
			$written = true;
			if (!($fp = @fopen($gallery_url->_return_file($import_schema, 'import', ''), 'w')))
			{
				$written = false;
			}
			if (!(@fwrite($fp, $import_file)))
			{
				$written = false;
			}
			@fclose($fp);
		}
	}

	function log_import_error($import_schema, $error)
	{
		global $phpbb_ext_gallery;

		$error_file = $phpbb_ext_gallery->url->_return_file($import_schema . '_errors', 'import', '');
		$content = @file_get_contents($error_file);
		file_put_contents($error_file, $content .= (($content) ? "\n" : '') . $error);
	}
}
