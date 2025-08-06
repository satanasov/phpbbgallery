<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\core\ucp;

/**
 * Info file for UCP Module for the User base settings
 * @package phpbbgallery\core\ucp
 */
class settings_info
{
	function module()
	{
		return array(
			'filename'	=> '\phpbbgallery\core\ucp\settings_module',
			'title'		=> 'PHPBB_GALLERY',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'manage'		=> array('title' => 'UCP_GALLERY_SETTINGS', 'auth' => 'ext_phpbbgallery/core', 'cat' => array('PHPBB_GALLERY')),
			),
		);
	}
}
