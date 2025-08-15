<?php
/**
 * phpBB Gallery - ACP Import Extension
 *
 * @package   phpbbgallery/acpimport
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
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

		$phpbbgallery_import_file = $phpbb_root_path . 'files/phpbbgallery/import';

		if (!is_dir($phpbbgallery_import_file))
		{
			if (is_writable($phpbb_root_path . 'files'))
			{
				@mkdir($phpbbgallery_import_file, 0755, true);
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

		$phpbbgallery_import_file = $phpbb_root_path . 'files/phpbbgallery/import';

		// Clean dirs
		if (is_dir($phpbbgallery_import_file))
		{
			$this->recursiveRemoveDirectory($phpbbgallery_import_file);
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
