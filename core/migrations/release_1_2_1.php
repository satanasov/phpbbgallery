<?php
/**
 * Created by PhpStorm.
 * User: lucifer
 * Date: 17.9.2017 г.
 * Time: 0:44
 */

namespace phpbbgallery\core\migrations;


class release_1_2_1 extends \phpbb\db\migration\profilefield_base_migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_2_0');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array(&$this, 'install_config'))),
			array('custom', array(array($this, 'create_custom_field'))),
			array('custom', array(array(&$this, 'add_base_url'))),
		);
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

	public function add_base_url()
	{
		global $config;
		$base_URI = $config['server_protocol'];
		$base_URI .= $config['server_name'];
		$base_URI .= ($config['server_port'] != 80 ? ':' . $config['server_port'] : '');
		$base_URI .= $config['script_path'];
		$base_URI .= ($config['enable_mod_rewrite'] == 0 ? '/app.php' : '');
		$base_URI .= '/gallery/album/%s';
		$sql = 'UPDATE ' . PROFILE_FIELDS_TABLE . ' SET field_contact_url = \'' . $base_URI . '\' WHERE field_name = \'gallery_palbum\'';
		$this->db->sql_query($sql);
	}

	static public $configs = array(
		'version'					=> '1.2.1',
		'disp_gallery_icon'			=> true,
	);

	protected $profilefield_name = 'gallery_palbum';
	protected $profilefield_database_type = array('VCHAR', '');
	protected $profilefield_data = array(
		'field_name'	=> 'gallery_palbum',
		'field_type'	=> 'profilefields.type.string',
		'field_ident'	=> 'gallery_palbum',
		'field_length'	=> 8,
		'field_minlen'	=> 1,
		'field_maxlen'	=> 9,
		'field_novalue'	=> '',
		'field_default_value'	=> '',
		'field_validation'	=> '[0-9]+',
		'field_required'	=> 0,
		'field_show_novalue'	=> 0,
		'field_show_on_reg'	=> 0,
		'field_show_on_pm'	=> 1,
		'field_show_on_vt'	=> 1,
		'field_show_profile'	=> 1,
		'field_show_on_ml'	=> 0,
		'field_hide'	=> 0,
		'field_no_view'	=> 0,
		'field_active'	=> 1,
		'field_is_contact'	=> 1,
		'field_contact_desc'	=> 'VIEW_USER_GALLERY',
		'field_contact_url'	=> ''
	);
}