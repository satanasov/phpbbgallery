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
