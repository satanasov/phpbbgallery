<?php
/**
 * Created by PhpStorm.
 * User: lucifer
 * Date: 17.9.2017 г.
 * Time: 0:44
 */

namespace phpbbgallery\core\migrations;


class release_1_2_1 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_2_0');
	}

	public function install_config()
	{
		global $config;

		foreach (self::$configs as $name => $value)
		{
			if (isset(self::$is_dynamic[$name]))
			{
				$config->set('phpbb_gallery_' . $name, $value, true);
			}
			else
			{
				$config->set('phpbb_gallery_' . $name, $value);
			}
		}

		return true;
	}

	static public $configs = array(
		'version'					=> '1.2.1',
		'disp_gallery_icon'			=> true,
	);
}