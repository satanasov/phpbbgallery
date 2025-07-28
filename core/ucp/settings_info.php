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
