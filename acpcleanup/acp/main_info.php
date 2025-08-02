<?php
/**
*
* @package phpBB Gallery - ACP CleanUp Extension
* @copyright (c) 2012 nickvergessen  | 2025 Leinad4Mind
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\acpcleanup\acp;

/**
 * ACP Module Info class
 */
class main_info
{
	/**
	 * Returns module information
	 *
	 * @return array Module information
	 */
	public function module(): array
	{
		return [
				'filename' => '\phpbbgallery\acpcleanup\acp\main_module',
				'title'    => 'PHPBB_GALLERY',
				'version'  => '1.2.2',
				'modes'    => [
					'cleanup' => [
						'title' => 'ACP_GALLERY_CLEANUP',
						'auth'  => 'acl_a_gallery_cleanup && ext_phpbbgallery/acpcleanup',
						'cat'   => ['PHPBB_GALLERY'],
					],
				],
		];
	}
}
