<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace phpbbgallery\core\file\types;

use bantu\IniGetWrapper\IniGetWrapper;
use phpbb\files\factory;
use phpbb\files\filespec;
use phpbb\language\language;
use phpbb\plupload\plupload;
use phpbb\request\request_interface;

class multiform extends \phpbb\files\types\base
{
	/** @var factory Files factory */
	protected $factory;
	/** @var language */
	protected $language;
	/** @var IniGetWrapper */
	protected $php_ini;
	/** @var plupload */
	protected $plupload;
	/** @var request_interface */
	protected $request;
	/** @var \phpbb\files\upload */
	protected $upload;
	/**
	 * Construct a form upload type
	 *
	 * @param factory			$factory	Files factory
	 * @param language			$language	Language class
	 * @param IniGetWrapper		$php_ini	ini_get() wrapper
	 * @param plupload			$plupload	Plupload
	 * @param request_interface	$request	Request object
	 */
	public function __construct(factory $factory, language $language, IniGetWrapper $php_ini, plupload $plupload, request_interface $request)
	{
		$this->factory = $factory;
		$this->language = $language;
		$this->php_ini = $php_ini;
		$this->plupload = $plupload;
		$this->request = $request;
	}
	/**
	 * {@inheritdoc}
	 */
	public function upload()
	{
		$args = func_get_args();
		return $this->form_upload($args[0]);
	}
	/**
	 * Form upload method
	 * Upload file from users harddisk
	 *
	 * @param string $form_name Form name assigned to the file input field (if it is an array, the key has to be specified)
	 *
	 * @return filespec $file Object "filespec" is returned, all further operations can be done with this object
	 * @access public
	 */
	protected function form_upload($form_name)
	{

		$uploads = ($this->request->variable($form_name, array('name'=> array('' => ''), 'type' => array('' => ''), 'tmp_name' => array('' => ''), 'error' =>  array('' => ''), 'size' => array('' => '')), true, \phpbb\request\request_interface::FILES));
		$upload_redy = array();
		for ($i = 0; $i < count($uploads['name']); $i++)
		{
			$upload_redy[$i] = array(
				'name' => $uploads['name'][$i],
				'type' => $uploads['type'][$i],
				'tmp_name' => $uploads['tmp_name'][$i],
				'error'	=> ($uploads['error'][$i] ? $uploads['error'][$i] : null),
				'size'	=> $uploads['size'][$i]
			);
		}
		$files = array();
		foreach ($upload_redy as $ID => $VAR)
		{
			$upload = array(
				'name' => $VAR['name'],
				'type' => $VAR['type'],
				'tmp_name' => $VAR['tmp_name'],
				'error'	=> $VAR['error'],
				'size'	=> $VAR['size']
			);

			$file = $this->factory->get('filespec')
				->set_upload_ary($upload)
				->set_upload_namespace($this->upload);

			if ($file->init_error())
			{
				$file->error[] = '';
				$files[$ID] = $file;
				continue;
			}
			// Error array filled?
			if (isset($upload['error']))
			{
				$error = $this->upload->assign_internal_error($upload['error']);

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
				$file->error[] = $this->language->lang($this->upload->error_prefix . 'EMPTY_FILEUPLOAD');
				$files[$ID] = $file;
				continue;
			}

			// PHP Upload file size check
			$file = $this->check_upload_size($file);
			if (sizeof($file->error))
			{
				$files[$ID] = $file;
				continue;
			}

			// Not correctly uploaded
			if (!$file->is_uploaded())
			{
				$file->error[] = $this->language->lang($this->upload->error_prefix . 'NOT_UPLOADED');
				$files[$ID] = $file;
				continue;
			}
			$this->upload->common_checks($file);
			$files[$ID] = $file;
			continue;
		}

		return $files;
	}
}