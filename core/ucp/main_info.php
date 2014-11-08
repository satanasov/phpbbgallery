<?php

/**
*
* @package phpBB Gallery
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\ucp;

/**
* @package module_install
*/
class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\phpbbgallery\core\ucp\main_module',
			'title'		=> 'PHPBB_GALLERY',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'manage_albums'			=> array(
					'title' => 'UCP_GALLERY_PERSONAL_ALBUMS',
					'auth' => 'ext_phpbbgallery/core',
					'cat' => array('PHPBB_GALLERY')
				),
				'manage_subscriptions'	=> array(
					'title' => 'UCP_GALLERY_WATCH',
					'auth' => 'ext_phpbbgallery/core',
					'cat' => array('PHPBB_GALLERY')
				),
			),
		);
	}
}
