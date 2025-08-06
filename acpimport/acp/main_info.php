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

class main_info
{
	function module()
	{
		return array(
			'filename'	=> 'main_module',
			'title'		=> 'PHPBB_GALLERY',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'import_images'		=> array(
					'title' => 'ACP_IMPORT_ALBUMS',
					'auth' => 'acl_a_gallery_import && ext_phpbbgallery/acpimport',
					'cat' => array('PHPBB_GALLERY')
				),
			),
		);
	}
}
