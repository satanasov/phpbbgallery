<?php
/**
*
* @package phpBB Gallery ACP Import
* @copyright (c) 2014 satanasov
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\acpimport\migrations;

class m1_init extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_2_0');
	}

	public function revert_data()
	{
		return array(
			array('custom', array(array(&$this, 'remove_file_system'))),
		);
	}

	public function update_data()
	{
		return array(
			array('permission.add', array('a_gallery_import', true, 'a_board')),
			array('module.add', array(
				'acp',
				'PHPBB_GALLERY',
				array(
					'module_basename'	=> '\phpbbgallery\acpimport\acp\main_module',
					'module_langname'	=> 'ACP_IMPORT_ALBUMS',
					'module_mode'		=> 'import_images',
					'module_auth'		=> 'ext_phpbbgallery/acpimport && acl_a_gallery_import',
				)
			)),
			array('custom', array(array(&$this, 'create_file_system'))),
		);
	}

	public function create_file_system()
	{
		global $phpbb_root_path;

		$phpbbgallery_core_file_import = $phpbb_root_path . 'files/phpbbgallery/import';

		if (is_writable($phpbb_root_path . 'files'))
		{
			@mkdir($phpbbgallery_core_file_import, 0755, true);
		}
	}

	public function remove_file_system()
	{
		global $phpbb_root_path;

		$phpbbgallery_core_file_import = $phpbb_root_path . 'files/phpbbgallery/import';

		// Clean dirs
		$this->recursiveRemoveDirectory($phpbbgallery_core_file_import);
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
