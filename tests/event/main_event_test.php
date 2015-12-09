<?php
/**
*
* phpBB Gallery events test
*
* @copyright (c) 2015 Lucifer <https://www.anavaro.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbbgallery\tests\event;

/**
* @group event
*/

class main_event_test extends \phpbb_database_test_case
{
	protected $listener;
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
		parent::setUp();

		$this->controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();
		
		$this->user = $this->getMock('\phpbb\user', array(), array('\phpbb\datetime'));
		$this->user->optionset('viewcensors', false);
		$this->user->style['style_path'] = 'prosilver';

		$this->gallery_search = $this->getMockBuilder('\phpbbgallery\core\search')
			->disableOriginalConstructor()
			->getMock();
		
		$this->config = new \phpbb\config\config(array());
		
		$this->gallery_config = new \phpbbgallery\core\config($this->config);
		
		$this->db = $this->new_dbal();
	}

	/**
	* Create our controller
	*/
	protected function set_listener()
	{
		$this->listener = new \phpbbgallery\core\event\main_listener(
			$this->controller_helper,
			$this->template,
			$this->user,
			$this->gallery_search,
			$this->gallery_config,
			$this->db,
			'php',
			'phpbb_gallery_albums',
			'phpbb_gallery_users'
		);
	}

	/**
	* Test the event listener is subscribing events
	*/
	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.user_setup',
			'core.page_header',
			'core.memberlist_view_profile',
			'core.generate_profile_fields_template_data_before',
			'core.grab_profile_fields_data',
		), array_keys(\phpbbgallery\core\event\main_listener::getSubscribedEvents()));
	}

	public function load_language_on_setup_data()
	{
		return array(
			'normal'	=> array(
				array(	//This is config
					'phpbb_gallery_disp_total_images' => 1,
					'phpbb_gallery_num_images'	=> 1,
				),
				1, // Expected count
				array( // expected array
					'PHPBBGALLERY_INDEX_STATS'	=> 1
				)
			),
			'default'	=> array(
				array(	//This is config
				),
				1, // Expected count
				array( // expected array
					'PHPBBGALLERY_INDEX_STATS'	=> 0
				)
			),
			'disable'	=> array(
				array(	//This is config
					'phpbb_gallery_disp_total_images' => 0,
				),
				0, // Expected count
				array( // expected array
					'PHPBBGALLERY_INDEX_STATS'	=> 0
				)
			)
		);
	}
	/**
	* Test load_language_on_setup (only test display total images)
	*
	* @dataProvider load_language_on_setup_data
	*/
	public function test_load_language_on_setup($config, $count, $expected)
	{
		foreach($config as $id => $state)
		{
			$this->config[$id] = $state;
		}
		$this->set_listener();
		$this->template->expects($this->exactly($count))
			->method('assign_vars')
			->with($expected);
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.user_setup', array($this->listener, 'load_language_on_setup'));
		$dispatcher->dispatch('core.user_setup');
	}
	
	/**
	* Test add_page_header_link
	*/
	public function test_add_page_header_link()
	{
		$this->set_listener();
		$this->template->expects($this->once())
			->method('assign_vars')
			->with(array(
				'U_GALLERY'	=> $this->controller_helper->route('phpbbgallery_index'),
			));
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.page_header', array($this->listener, 'add_page_header_link'));
		$dispatcher->dispatch('core.page_header');
	}

	/**
	* Test user_profile_galleries

	public function test_user_profile_galleries()
	{
		$member = array('user_id' => 2);
		$event_data = array('member');
		$event = new \phpbb\event\data(compact($event_data));
		$this->config['phpbb_gallery_rrc_profile_mode'] = 3;
		$this->config['phpbb_gallery_rrc_profile_items'] = 3;
		$this->set_listener();
		$this->gallery_search->expects($this->once())
			->method('recent');
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.memberlist_view_profile', array($this->listener, 'user_profile_galleries'));
		$dispatcher->dispatch('core.memberlist_view_profile', $event);
	}	*/	
}
