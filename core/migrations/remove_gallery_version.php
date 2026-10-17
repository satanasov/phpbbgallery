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

class remove_gallery_version extends migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_3_4_0');
	}

	public function update_data()
	{
		return array(
			array('config.remove', array('phpbb_gallery_version')),
		);
	}
}
