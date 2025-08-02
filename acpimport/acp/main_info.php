<?php
/**
*
* @package phpBB Gallery - ACP Import Extension
* @copyright (c) 2012 nickvergessen | 2025 Leinad4Mind
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\acpimport\acp;

/**
 * ACP Module Info class for Gallery Import
 */
class main_info
{
	/**
	 * Returns module information
	 *
	 * @return array Module configuration
	 */
	public function module(): array
	{
		return [
				'filename' => '\phpbbgallery\acpimport\acp\main_module',
				'title'    => 'PHPBB_GALLERY',
				'version'  => '1.2.2',
				'modes'    => [
					'import_images' => [
						'title' => 'ACP_IMPORT_ALBUMS',
						'auth'  => 'ext_phpbbgallery/acpimport && acl_a_gallery_import',
						'cat'   => ['PHPBB_GALLERY'],
					],
				],
		];
	}
}
