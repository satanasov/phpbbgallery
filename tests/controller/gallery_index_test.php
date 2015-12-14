<?php
/**
*
* phpBB Gallery extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Lucifer <https://www.anavaro.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbbgallery\tests\controller;

/**
* @group controller
*/

require_once dirname(__FILE__) . '/../../../../includes/functions.php';
require_once dirname(__FILE__) . '/../../../../includes/functions_content.php';

class gallery_index_test extends \phpbb_database_test_case
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
		
		global $phpbb_dispatcher, $auth, $user, $cache;

		//Let's build some deps
		$this->auth = $this->getMock('\phpbb\auth\auth');
		
		$auth = $this->auth;
		
		$config = $this->config = new \phpbb\config\config(array());
		
		$this->db = $this->new_dbal();
		
		$this->request = $this->getMock('\phpbb\request\request');
		
		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();
			
		$this->user = $this->getMock('\phpbb\user', array(), array('\phpbb\datetime'));
		
		$user = $this->user;
		
		$this->controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
		$this->controller_helper->expects($this->any())
			->method('render')
			->willReturnCallback(function ($template_file, $page_title = '', $status_code = 200, $display_online_list = false) {
				return new \Symfony\Component\HttpFoundation\Response($template_file, $status_code);
			});
		
		$cache = $this->cache = new \phpbb\cache\service(
			new \phpbb\cache\driver\null(),
			$this->config,
			$this->db,
			$phpbb_root_path,
			$phpEx
		);

		$this->pagination = $this->getMockBuilder('\phpbb\pagination')
			->disableOriginalConstructor()
			->getMock();
		
		$phpbb_dispatcher = $this->dispatcher = new \phpbb_mock_event_dispatcher();
		
		$this->user_loader = $this->getMockBuilder('\phpbb\user_loader')
			->disableOriginalConstructor()
			->getMock();
			
		$this->gallery_cache = new \phpbbgallery\core\cache(
			$this->cache,
			$this->db,
			'phpbb_gallery_albums',
			'phpbb_gallery_images'
		);
		
		$this->gallery_user = new \phpbbgallery\core\user(
			$this->db,
			$this->dispatcher,
			'phpbb_gallery_users'
		);
		
		// Let's build auth class
		$this->gallery_auth = new \phpbbgallery\core\auth\auth(
			$this->gallery_cache,
			$this->db,
			$this->gallery_user,
			$this->user,
			$this->auth,
			'phpbb_gallery_permissions',
			'phpbb_gallery_roles',
			'phpbb_gallery_users',
			'phpbb_gallery_albums'
		);
		
		$this->misc = $this->getMockBuilder('\phpbbgallery\core\misc')
			->disableOriginalConstructor()
			->getMock();
		
		$this->display = new \phpbbgallery\core\album\display(
			$this->auth,
			$this->config,
			$this->controller_helper,
			$this->db,
			$this->pagination,
			$this->request,
			$this->template,
			$this->user,
			$this->gallery_auth,
			$this->gallery_user,
			$this->misc,
			'/',
			'php',
			'phpbb_gallery_albums',
			'phpbb_gallery_contests',
			'phpbb_gallery_albums_track',
			'phpbb_gallery_albums_modscache'
		);

		$this->gallery_album = new \phpbbgallery\core\album\album(
			$this->db,
			$this->user,
			'phpbb_gallery_albums',
			'phpbb_gallery_watch',
			'phpbb_gallery_contests'
		);

		$this->gallery_config = new \phpbbgallery\core\config(
			$this->config
		);
		
		$this->gallery_image = $this->getMockBuilder('\phpbbgallery\core\image\image')
			->disableOriginalConstructor()
			->getMock();
		$this->gallery_image->method('get_status_orphan')
			->willReturn(3);

		// Let's build Search
		$this->gallery_search = new \phpbbgallery\core\search(
			$this->db,
			$this->template,
			$this->user,
			$this->controller_helper,
			$this->gallery_config,
			$this->gallery_auth,
			$this->gallery_album,
			$this->gallery_image,
			$this->pagination,
			$this->user_loader,
			'phpbb_gallery_images',
			'phpbb_gallery_albums',
			'phpbb_gallery_comments'
		);
	}
	public function test_install()
	{
		$db_tools = new \phpbb\db\tools($this->db);
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_albums'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_albums_track'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_comments'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_contests'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_favorites'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_images'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_modscache'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_permissions'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_rates'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_reports'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_roles'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_users'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_watch'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_log'));
	}
	
	public function get_controller($user_id, $grpup, $is_registered)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group'] = $group;
		$this->user->data['is_registered'] = $is_registered;
		$controller = new \phpbbgallery\core\controller\index(
			$this->auth,
			$this->config,
			$this->db,
			$this->request,
			$this->template,
			$this->user,
			$this->controller_helper,
			$this->display,
			$this->gallery_config,
			$this->gallery_auth,
			$this->gallery_search,
			$this->pagination,
			$this->gallery_user,
			$this->gallery_image,
			'./',
			'php'
		);
		
		return $controller;
	}

	/**
	* Data for test index controller base function
	*/
	public function controller_base_data()
	{
		return array(
			'admin_no_gindex' => array(
				2, // User ID
				5, // User Group
				true, // Is registered
				0, // rrc_gindex_mode
				0, // disp_birthdays
				3, // Expected calls of assign_block_vars method
				6, // Expected calls of the assign_vars method
			),
			'user_no_gindex' => array(
				53, // User ID
				2, // User Group
				true, // Is registered
				0, // rrc_gindex_mode
				0, // disp_birthdays
				1, // Expected calls of assign_block_vars method
				5, // Expected calls of the assign_vars method
			),
		);
	}
	/**
	* Test controller index
	* function base()
	*
	* @dataProvider controller_base_data
	*/
	public function test_controller_base($user_id, $group, $is_registered, $rrc_gindex_mode, $disp_birthdays, $exp_block_vars, $exp_vars)
	{
		$this->template->expects($this->exactly($exp_block_vars))
			->method('assign_block_vars');
		$this->template->expects($this->exactly($exp_vars))
			->method('assign_vars');
		$this->gallery_config->set('rrc_gindex_mode', $rrc_gindex_mode);
		$this->gallery_config->set('disp_birthdays', $disp_birthdays);
		$controller = $this->get_controller($user_id, $group, $is_registered);
		$response = $controller->base();
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}
}