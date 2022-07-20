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

class controller_base extends \phpbb_database_test_case
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
	public function setUp() : void
	{
		global $request, $phpbb_root_path, $phpEx;
		parent::setUp();
		//Let's build some deps
		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->disableOriginalConstructor()
			->getMock();

		$auth = $this->auth;

		$config = $this->config = new \phpbb\config\config(array());

		$this->db = $this->new_dbal();
		$db = $this->db;

		$request = $this->request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();

		$this->phpbb_dispatcher = new \phpbb_mock_event_dispatcher();

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->disableOriginalConstructor()
			->getMock();

		$this->language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();
		$this->language->method('lang')
			->will($this->returnArgument(0));

		$this->user = $this->getMockBuilder('\phpbb\user')
			->setConstructorArgs(array(
				new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
				'\phpbb\datetime'
			))
			->getMock();
		$this->user
			->method('lang')
			->will($this->returnArgument(0));
		$this->user->expects($this->any())
			->method('create_datetime')
			->will($this->returnCallback(array($this, 'create_datetime_callback')));
		$this->user->timezone = new \DateTimeZone('UTC');
		$this->user->lang = array(
			'datetime' => array(),
			'DATE_FORMAT' => 'm/d/Y',
		);

		$user = $this->user;

		$this->controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
		$this->controller_helper->expects($this->any())
			->method('render')
			->willReturnCallback(function ($template_file, $page_title = '', $status_code = 200, $display_online_list = false) {
				return new \Symfony\Component\HttpFoundation\Response($template_file, $status_code);
			});
		$this->controller_helper
			->method('route')
			->will($this->returnArgument(0));

		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();

		$cache = $this->cache = new \phpbb\cache\service(
			new \phpbb\cache\driver\dummy(),
			$this->config,
			$this->db,
			$phpbb_dispatcher,
			$phpbb_root_path,
			$phpEx
		);

		$this->cache->purge();

		$this->pagination = $this->getMockBuilder('\phpbb\pagination')
			->disableOriginalConstructor()
			->getMock();

		$this->user_cpf = $this->getMockBuilder('\phpbb\profilefields\manager')
			->disableOriginalConstructor()
			->getMock();

		$phpbb_dispatcher = $this->dispatcher = new \phpbb_mock_event_dispatcher();
		$this->phpbb_container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
			->disableOriginalConstructor()
			->getMock();

		$this->user_loader = $this->getMockBuilder('\phpbb\user_loader')
			->disableOriginalConstructor()
			->getMock();

		$this->gallery_notification_helper = $this->getMockBuilder('\phpbbgallery\core\notification\helper')
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
			$this->user,
			$this->user_cpf,
			$this->config,
			$this->auth,
			'phpbb_gallery_users',
			'/',
			'php'
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

		$this->gallery_auth_level = new \phpbbgallery\core\auth\level(
			$this->gallery_auth,
			$this->config,
			$this->template,
			$this->user,
			$this->language
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
			$this->language,
			$this->gallery_auth,
			$this->gallery_user,
			$this->misc,
			'/',
			'php',
			'phpbb_gallery_albums',
			'phpbb_gallery_contests',
			'phpbb_gallery_albums_track',
			'phpbb_gallery_modscache'
		);

		$this->gallery_config = new \phpbbgallery\core\config(
			$this->config
		);

		$this->gallery_contest = new \phpbbgallery\core\contest(
			$this->db,
			$this->gallery_config,
			'phpbb_images_table',
			'phpbb_contests_table'
		);

		$this->gallery_loader = new \phpbbgallery\core\album\loader(
			$this->db,
			$this->user,
			$this->gallery_contest,
			'phpbb_gallery_albums'
		);

		$this->block = new \phpbbgallery\core\block();

		$this->gallery_album = new \phpbbgallery\core\album\album(
			$this->db,
			$this->user,
			$this->language,
			$this->user_cpf,
			$this->gallery_auth,
			$this->gallery_cache,
			$this->block,
			$this->gallery_config,
			'phpbb_gallery_albums',
			'phpbb_gallery_images',
			'phpbb_gallery_watch',
			'phpbb_gallery_contests'
		);

		$this->gallery_url = new \phpbbgallery\core\url(
			$this->template,
			$this->request,
			$this->config,
			'/',
			'adm'
		);

		$this->log = new \phpbbgallery\core\log(
			$this->db,
			$this->user,
			$this->language,
			$this->user_loader,
			$this->template,
			$this->controller_helper,
			$this->pagination,
			$this->gallery_auth,
			$this->gallery_config,
			'phpbb_gallery_log',
			'phpbb_gallery_images'
		);

		$this->gallery_report = new \phpbbgallery\core\report(
			$this->log,
			$this->gallery_auth,
			$this->user,
			$this->language,
			$this->db,
			$this->user_loader,
			$this->gallery_album,
			$this->template,
			$this->controller_helper,
			$this->gallery_config,
			$this->pagination,
			$this->gallery_notification_helper,
			'phpbb_gallery_images',
			'phpbb_gallery_reports'
		);

		$this->gallery_file = new \phpbbgallery\core\file\file(
			$this->request,
			$this->gallery_url,
			$this->gallery_config,
			2
		);

		$this->gallery_image = new \phpbbgallery\core\image\image(
			$this->db,
			$this->user,
			$this->language,
			$this->template,
			$this->phpbb_dispatcher,
			$this->gallery_auth,
			$this->gallery_album,
			$this->gallery_config,
			$this->controller_helper,
			$this->gallery_url,
			$this->log,
			$this->gallery_notification_helper,
			$this->gallery_report,
			$this->gallery_cache,
			$this->gallery_user,
			$this->gallery_contest,
			$this->gallery_file,
			'phpbb_gallery_images'
		);

		// Let's build Search
		$this->gallery_search = new \phpbbgallery\core\search(
			$this->db,
			$this->template,
			$this->user,
			$this->language,
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

		$this->gallery_comment = new \phpbbgallery\core\comment(
			$this->user,
			$this->db,
			$this->gallery_config,
			$this->gallery_auth,
			$this->block,
			'phpbb_gallery_comments',
			'phpbb_gallery_images'
		);

		$this->gallery_rating = new \phpbbgallery\core\rating(
			$this->db,
			$this->template,
			$this->user,
			$this->language,
			$this->request,
			$this->gallery_config,
			$this->gallery_auth,
			'phpbb_gallery_images',
			'phpbb_gallery_albums',
			'phpbb_gallery_rates'
		);

		$this->gallery_notification = new \phpbbgallery\core\notification(
			$this->db,
			$this->user,
			'phpbb_gallery_watch'
		);
		$this->gallery_moderate = new \phpbbgallery\core\moderate(
			$this->db,
			$this->template,
			$this->controller_helper,
			$this->user,
			$this->language,
			$this->user_loader,
			$this->gallery_album,
			$this->gallery_auth,
			$this->pagination,
			$this->gallery_comment,
			$this->gallery_report,
			$this->gallery_image,
			$this->gallery_config,
			$this->gallery_notification,
			$this->gallery_rating,
			'phpbb_gallery_images'
		);
	}
}
