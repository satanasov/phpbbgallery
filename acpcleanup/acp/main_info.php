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
