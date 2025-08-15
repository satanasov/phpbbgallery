<?php
/**
 * phpBB Gallery - ACP CleanUp Extension
 *
 * @package   phpbbgallery/acpcleanup
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\acpcleanup\migrations;

use phpbb\db\migration\migration;

class m1_init extends migration
{
	static public function depends_on()
	{
		return ['\phpbbgallery\core\migrations\release_1_2_0'];
	}

	public function update_data()
	{
		return [
				['permission.add', ['a_gallery_cleanup', true, 'a_board']],
				['module.add', [
					'acp',
					'PHPBB_GALLERY',
					[
						'module_basename' => '\phpbbgallery\acpcleanup\acp\main_module',
						'module_langname' => 'ACP_GALLERY_CLEANUP',
						'module_mode'     => 'cleanup',
						'module_auth'     => 'ext_phpbbgallery/acpcleanup && acl_a_gallery_cleanup',
					]
				]],
		];
	}
}
