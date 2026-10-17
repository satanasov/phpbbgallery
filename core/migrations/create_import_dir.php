<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    Leinad4Mind
 * @copyright 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\core\migrations;

use phpbb\db\migration\migration;

class create_import_dir extends migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\remove_gallery_version');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array(&$this, 'create_import_dir'))),
		);
	}

	/**
	 * ZIP uploads are extracted below files/phpbbgallery/import, which was previously
	 * only created by the acpimport extension.
	 * It is removed together with files/phpbbgallery when the core is purged.
	 */
	public function create_import_dir()
	{
		global $phpbb_root_path;

		$phpbbgallery_import_file = $phpbb_root_path . 'files/phpbbgallery/import';

		if (!is_dir($phpbbgallery_import_file) && is_writable($phpbb_root_path . 'files'))
		{
			@mkdir($phpbbgallery_import_file, 0755, true);
		}
	}
}
