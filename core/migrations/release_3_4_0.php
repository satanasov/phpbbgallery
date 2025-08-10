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

class release_3_4_0 extends migration
{
	static public function depends_on()
	{
		return ['\phpbbgallery\core\migrations\release_1_2_0_db_create'];
	}

	public function update_data()
	{
		return [
			['config.update', ['phpbb_gallery_version', '3.4.0']]
		];
	}

}
