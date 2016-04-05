<?php
/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\migrations;

class release_1_2_0_create_filesystem extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_2_0_add_bbcode');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array(&$this, 'create_file_system'))),
			array('custom', array(array(&$this, 'copy_images'))),
		);
	}

	public function revert_data()
	{
		return array(
			array('custom', array(array(&$this, 'remove_file_system'))),
		);
	}

	public function create_file_system()
	{
		global $phpbb_root_path;

		$phpbbgallery_core_file = $phpbb_root_path . 'files/phpbbgallery/core';
		$phpbbgallery_core_file_medium = $phpbb_root_path . 'files/phpbbgallery/core/medium';
		$phpbbgallery_core_file_mini = $phpbb_root_path . 'files/phpbbgallery/core/mini';
		$phpbbgallery_core_file_source = $phpbb_root_path . 'files/phpbbgallery/core/source';
		$phpbbgallery_import_file = $phpbb_root_path . 'files/phpbbgallery/import';

		if (is_writable($phpbb_root_path . 'files'))
		{
			@mkdir($phpbbgallery_core_file, 0755, true);
			@mkdir($phpbbgallery_core_file_medium, 0755, true);
			@mkdir($phpbbgallery_core_file_mini, 0755, true);
			@mkdir($phpbbgallery_core_file_source, 0755, true);
			@mkdir($phpbbgallery_import_file, 0755, true);
		}
	}

	public function remove_file_system()
	{
		global $phpbb_root_path;

		$phpbbgallery_core_file = $phpbb_root_path . 'files/phpbbgallery/core';
		$phpbbgallery_core_file_medium = $phpbb_root_path . 'files/phpbbgallery/core/medium';
		$phpbbgallery_core_file_mini = $phpbb_root_path . 'files/phpbbgallery/core/mini';
		$phpbbgallery_core_file_source = $phpbb_root_path . 'files/phpbbgallery/core/source';
		$phpbbgallery_import_file = $phpbb_root_path . 'files/phpbbgallery/import';

		// Clean dirs
		$this->recursiveRemoveDirectory($phpbbgallery_core_file_mini);
		$this->recursiveRemoveDirectory($phpbbgallery_core_file_medium);
		$this->recursiveRemoveDirectory($phpbbgallery_core_file_source);
		$this->recursiveRemoveDirectory($phpbbgallery_core_file);
		$this->recursiveRemoveDirectory($phpbb_root_path . 'files/phpbbgallery');
		$this->recursiveRemoveDirectory($phpbbgallery_import_file);
	}

	public function copy_images()
	{
		global $phpbb_root_path;
		$phpbbgallery_core_file_source = $phpbb_root_path . 'files/phpbbgallery/core/source';
		$phpbbgallery_core_images_source = $phpbb_root_path . 'ext/phpbbgallery/core/images';
		copy($phpbbgallery_core_images_source . '/upload/image_not_exist.jpg', $phpbbgallery_core_file_source . '/image_not_exist.jpg');
		copy($phpbbgallery_core_images_source . '/upload/no_hotlinking.jpg', $phpbbgallery_core_file_source . '/no_hotlinking.jpg');
		copy($phpbbgallery_core_images_source . '/upload/not_authorised.jpg', $phpbbgallery_core_file_source . '/not_authorised.jpg');
	}
	function recursiveRemoveDirectory($directory)
	{
		foreach (glob("{$directory}/*") as $file)
		{
			if (is_dir($file))
			{
				$this->recursiveRemoveDirectory($file);
			}
			else
			{
				unlink($file);
			}
		}
		rmdir($directory);
	}
}
