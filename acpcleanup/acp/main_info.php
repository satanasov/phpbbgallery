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

class main_info
{
	function module()
	{
		return array(
			'filename'	=> 'main_module',
			'title'		=> 'PHPBB_GALLERY',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'cleanup'			=> array(
					'title' => 'ACP_GALLERY_CLEANUP',
					'auth' => 'acl_a_gallery_cleanup && ext_phpbbgallery/acpcleanup',
					'cat' => array('PHPBB_GALLERY')
				),
			),
		);
	}
}
