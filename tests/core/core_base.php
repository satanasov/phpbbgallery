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
require_once dirname(__FILE__) . '/../../../../includes/functions_content.php';

class core_base extends \phpbb_database_test_case
{
	/**
	* Define the extensions to be tested
	*
	* @return array vendor/name of extension(s) to test
	*/
	static protected function setup_extensions()
	{
		return array('phpbbgallery/core');
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
		global $config, $phpbb_dispatcher, $user, $cache, $request, $db, $phpbb_root_path, $phpEx;



		/*$sql = 'DELETE FROM phpbb_config;';
		$sql .= 'DELETE FROM phpbb_gallery_albums;';
		$sql .= 'DELETE FROM phpbb_gallery_comments;';
		$sql .= 'DELETE FROM phpbb_gallery_images;';
		$sql .= 'DELETE FROM phpbb_gallery_permissions;';
		$sql .= 'DELETE FROM phpbb_gallery_roles;';
		$sql .= 'DELETE FROM phpbb_gallery_users;';
		$sql .= 'DELETE FROM phpbb_groups;';
		$sql .= 'DELETE FROM phpbb_users;';
		$sql .= 'DELETE FROM phpbb_user_group;';
		$sql .= 'DELETE FROM phpbb_zebra;';

		$this->db->sql_query($sql);*/

		parent::setUp();

		$db = $this->db = $this->new_dbal();

		$config = $this->config = new \phpbb\config\config(array());
		$phpbb_dispatcher = $this->dispatcher = new \phpbb_mock_event_dispatcher();

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();

		$this->language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();
		$this->language->method('lang')
			->will($this->returnArgument(0));

		$this->user = $this->getMock('\phpbb\user', array(), array(
			new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
			'\phpbb\datetime'
		));
		$user = $this->user;

		$this->auth = $this->getMock('\phpbb\auth\auth');

		$this->controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
		// Define some controller_helper stuff
		$this->controller_helper->method('route')->will($this->returnArgument(0));

		$controller_helper = $this->controller_helper;

		$cache = $this->cache = new \phpbb\cache\service(
			new \phpbb\cache\driver\dummy(),
			$this->config,
			$this->db,
			$phpbb_root_path,
			$phpEx
		);

		$this->cache->purge();

		$this->pagination = $this->getMockBuilder('\phpbb\pagination')->disableOriginalConstructor()
			->getMock();

		//$this->user_loader = new \phpbb\user_loader($this->db, __DIR__ . '/../../../', 'php', 'phpbb_users');
		$this->user_loader = $this->getMockBuilder('\phpbb\user_loader')
			->disableOriginalConstructor()
			->getMock();
		$this->user_loader->method('get_username')
			->will($this->returnArgument(0));

		$request = $this->request = $this->getMock('\phpbb\request\request');
	}

	protected function tearDown()
	{
		parent::tearDown();
	}
}