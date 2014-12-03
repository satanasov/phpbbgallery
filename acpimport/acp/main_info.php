<?php
/**
*
* @package Gallery - ACP Import Extension
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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
