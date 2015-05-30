<?php
/**
*
* PhpBB Gallery extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Lucifer <https://www.anavaro.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbbgallery\tests\core;
/**
* @group core
*/
require_once dirname(__FILE__) . '/../../../../includes/functions.php';

class core_base extends \phpbb_database_test_case
{
	/**
	* Define the extensions to be tested
	*
	* @return array vendor/name of extension(s) to test
	*/
	static protected function setup_extensions()
	{
		return array('phpbbgallery/core', 'phpbbgallery/exif');
	}
	
	protected $db;

	/**
	* Get data set fixtures
	*/
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/fixture.xml');
	}

	/**
	* Setup test environment
	*/
	public function setUp()
	{
		parent::setUp();

		global $config, $phpbb_dispatcher;
		
		$this->db = $this->new_dbal();

		$config = $this->config = new \phpbb\config\config(array());
		$phpbb_dispatcher = $this->dispatcher = new \phpbb_mock_event_dispatcher();
		
		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();
		
		$this->user = $this->getMock('\phpbb\user', array(), array('\phpbb\datetime'));
		$this->user->optionset('viewcensors', false);
		$this->user->style['style_path'] = 'prosilver';
		
		$controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
		
		$this->cache = new \phpbb\cache\service(
			new \phpbb\cache\driver\null(),
			$this->config,
			$this->db,
			$phpbb_root_path,
			$phpEx
		);
	}
}