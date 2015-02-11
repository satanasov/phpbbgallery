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
	
	public function config_set($option, $value)
	{
		$this->get_db();
		$sql = 'UPDATE ' . CONFIG_TABLE . '
			SET config_value = \'' . $value . '\'
			WHERE config_name = \'phpbb_gallery_' . $option . '\'';
		$this->db->sql_query($sql);
		$this->purge_cache();
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
}