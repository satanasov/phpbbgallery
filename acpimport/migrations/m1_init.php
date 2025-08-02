<?php
/**
 * phpBB Gallery - ACP Import Extension
 *
 * @package   phpBB Gallery
 * @copyright (c) 2014 satanasov | 2025 Leinad4Mind
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace phpbbgallery\acpimport\migrations;

use phpbb\db\migration\migration;

class m1_init extends migration
{
	/**
	 * Migration dependencies
	 *
	 * @return array Array of migration dependencies
	 */
	public static function depends_on(): array
	{
		return ['\phpbbgallery\core\migrations\release_1_2_0'];
	}

	/**
	 * Revert the changes
	 *
	 * @return array Array of update data
	 */
	public function revert_data(): array
	{
		return [
				['custom', [[&$this, 'remove_file_system']]],
		];
	}

	/**
	 * Update data
	 *
	 * @return array Array of update data
	 */
	public function update_data(): array
	{
		return [
				['permission.add', ['a_gallery_import', true, 'a_board']],
				['module.add', [
					'acp',
					'PHPBB_GALLERY',
					[
						'module_basename' => '\phpbbgallery\acpimport\acp\main_module',
						'module_langname' => 'ACP_IMPORT_ALBUMS',
						'module_mode'     => 'import_images',
						'module_auth'     => 'ext_phpbbgallery/acpimport && acl_a_gallery_import',
					]
				]],
				['custom', [[&$this, 'create_file_system']]],
		];
	}

	/**
	 * Create import directory
	 *
	 * @return void
	 */
	public function create_file_system(): void
	{
		global $phpbb_root_path;

		$phpbbgallery_core_file_import = $phpbb_root_path . 'files/phpbbgallery/import';

		if (!is_dir($phpbbgallery_core_file_import))
		{
			if (is_writable($phpbb_root_path . 'files'))
			{
				@mkdir($phpbbgallery_core_file_import, 0755, true);
			}
		}
	}

	/**
	 * Remove import directory
	 *
	 * @return void
	 */
	public function remove_file_system(): void
	{
		global $phpbb_root_path;

		$phpbbgallery_core_file_import = $phpbb_root_path . 'files/phpbbgallery/import';

		// Clean dirs
		if (is_dir($phpbbgallery_core_file_import))
		{
			$this->recursiveRemoveDirectory($phpbbgallery_core_file_import);
		}
	}

	/**
	 * Recursively remove a directory
	 *
	 * @param string $directory Directory path
	 * @return void
	 */
	private function recursiveRemoveDirectory(string $directory): void
	{
		if (!is_dir($directory))
		{
				return;
		}

		$files = new \FilesystemIterator($directory);
		foreach ($files as $file)
		{
				if ($file->isDir())
				{
					$this->recursiveRemoveDirectory($file->getPathname());
				}
				else
				{
					@unlink($file->getPathname());
				}
		}
		@rmdir($directory);
	}
}
