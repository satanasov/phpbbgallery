<?php
/**
*
* Gallery Tests
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
namespace phpbbgallery\tests\functional;
/**
* @group functional
*/
class phpbbgallery_base extends \phpbb_functional_test_case
{
	private $path;
	static protected function setup_extensions()
	{
		return array('phpbbgallery/core', 'phpbbgallery/exif', 'phpbbgallery/acpimport', 'phpbbgallery/acpcleanup');
	}
	public function setUp()
	{
		parent::setUp();
		$this->path = __DIR__ . '/images/';
		
	}	

	public function get_state($ext)
	{
		$this->get_db();
		$sql = 'SELECT ext_active
		FROM ' . EXT_TABLE .'
		WHERE ext_name = \'' . $ext . '\'';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return $row['ext_active'];
	}
	public function get_user_id($username)
	{
		$this->get_db();
		$sql = 'SELECT user_id, username 
				FROM ' . USERS_TABLE . '
				WHERE username_clean = \''.$this->db->sql_escape(utf8_clean_string($username)).'\'';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return $row['user_id'];
	}
	public function set_option($option, $value)
	{
		$this->get_db();
		$sql = "UPDATE phpbb_config
			SET config_value = " . $value ."
			WHERE config_name = 'phpbb_gallery_" . $option . "'";
		$this->db->sql_query($sql);
		$this->purge_cache();
	}
	public function get_url_from_meta($url)
	{
		$parts = explode(';', $url);
		$base = end($parts);
		
		return substr($base, 5);
	}
}