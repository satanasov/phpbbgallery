<?php
/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\migrations;

class m3_create_filesystem extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\m2_add_bbcode');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array(&$this, 'create_file_system'))),
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

		if (is_writable($phpbb_root_path . 'files'))
		{
			@mkdir($phpbbgallery_core_file, 0755, true);
			@mkdir($phpbbgallery_core_file_medium, 0755, true);
			@mkdir($phpbbgallery_core_file_mini, 0755, true);
			@mkdir($phpbbgallery_core_file_source, 0755, true);
		}
	}

	public function remove_file_system()
	{
		global $phpbb_root_path;

		$phpbbgallery_core_file = $phpbb_root_path . 'files/phpbbgallery/core';
		$phpbbgallery_core_file_medium = $phpbb_root_path . 'files/phpbbgallery/core/medium';
		$phpbbgallery_core_file_mini = $phpbb_root_path . 'files/phpbbgallery/core/mini';
		$phpbbgallery_core_file_source = $phpbb_root_path . 'files/phpbbgallery/core/source';

		// Clean dirs
		$this->recursiveRemoveDirectory($phpbbgallery_core_file_mini);
		$this->recursiveRemoveDirectory($phpbbgallery_core_file_medium);
		$this->recursiveRemoveDirectory($phpbbgallery_core_file_source);
		$this->recursiveRemoveDirectory($phpbbgallery_core_file);
		$this->recursiveRemoveDirectory($phpbb_root_path . 'files/phpbbgallery');
	}

	function recursiveRemoveDirectory($directory)
	{
		foreach(glob("{$directory}/*") as $file)
		{
			if(is_dir($file))
			{
				recursiveRemoveDirectory($file);
			}
			else {
				unlink($file);
			}
		}
		rmdir($directory);
	}
}
