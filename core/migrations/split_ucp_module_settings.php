<?php
/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\migrations;

class split_ucp_module_settings extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_1_6');
	}

	public function update_data()
	{
		return array(
			array('if', array(
				array('module.exists', array('ucp', 'UCP_GALLERY', 'UCP_GALLERY_SETTINGS')),
				array('module.remove', array('ucp', 'UCP_GALLERY', 'UCP_GALLERY_SETTINGS')),
			)),
			array('module.add', array('ucp', 'UCP_GALLERY', array(
				'module_basename'	=> '\phpbbgallery\core\ucp\settings_module',
				'module_langname'	=> 'UCP_GALLERY_SETTINGS',
				'module_mode'		=> 'manage',
				'module_auth'		=> 'ext_phpbbgallery/core',
			))),
		);
	}
}
