<?php
/**
*
* @package phpBB Gallery ACP Cleanup
* @copyright (c) 2014 satanasov | 2025 Leinad4Mind
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\acpcleanup\migrations;

class m1_init extends \phpbb\db\migration\migration
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
