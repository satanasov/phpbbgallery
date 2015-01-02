<?php
/**
*
* @package Gallery - ACP CleanUp Extension
* @copyright (c) 2012 nickvergessen - http://www.flying-bits.org/
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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
