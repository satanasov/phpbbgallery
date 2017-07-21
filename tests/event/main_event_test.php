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
		$this->controller_helper->method('route')->will($this->returnArgument(1));

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
			$this->language,
			$this->gallery_search,
			$this->gallery_config,
			$this->db,
			'phpbb_gallery_albums',
			'phpbb_gallery_users',
			'php'
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
				'U_GALLERY'	=> $this->controller_helper->route('phpbbgallery_core_index'),
			));
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.page_header', array($this->listener, 'add_page_header_link'));
		$dispatcher->dispatch('core.page_header');
	}

	public function user_profile_galleries_data()
	{
		return array(
			array(
				3,
				1
			),
			array(
				2,
				1
			),
			array(
				1,
				1
			),
			array(
				0,
				1
			),
			array(
				3,
				0
			),
			array(
				2,
				0
			),
			array(
				1,
				0
			),
			array(
				0,
				0
			),
		);
	}
	/**
	* Test user_profile_galleries
	*
	* @dataProvider user_profile_galleries_data
	*/
	public function test_user_profile_galleries($profile_mode, $profile_user_images)
	{
		$member = array('user_id' => 2);
		$event_data = array('member');
		$event = new \phpbb\event\data(compact($event_data));
		$this->config['phpbb_gallery_rrc_profile_mode'] = $profile_mode;
		$this->config['phpbb_gallery_rrc_profile_items'] = 3;
		$this->config['phpbb_gallery_profile_user_images'] = $profile_user_images;
		$this->set_listener();
		if ($profile_mode == 2 || $profile_mode == 3)
		{
			$this->gallery_search->expects($this->once())
				->method('random');
		}
		if ($profile_mode == 1 || $profile_mode == 3)
		{
			$this->gallery_search->expects($this->once())
				->method('recent');
		}
		if ($profile_user_images == 1)
		{
			$this->template->expects($this->once())
				->method('assign_vars')
				->with(array(
					'U_GALLERY_IMAGES_ALLOW'	=> true,
					'U_GALLERY_IMAGES'	=> 0
				));
		}
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.memberlist_view_profile', array($this->listener, 'user_profile_galleries'));
		$dispatcher->dispatch('core.memberlist_view_profile', $event);
	}

	public function gallery_link_in_profile_data()
	{
		return array(
			array(
				1, // profile_pega
				2, // user_id
				true //expect return
			),
			array(
				0, // profile_pega
				2, // user_id
				false //expect return
			),
			array(
				1, // profile_pega
				3, // user_id
				false //expect return
			),
			array(
				0, // profile_pega
				3, // user_id
				false //expect return
			),
		);
	}
	/**
	* Test events
	*
	* @dataProvider gallery_link_in_profile_data
	* profile_fileds
	* get_user_ids
	*/
	public function test_gallery_link_events($option, $user_id, $expect)
	{
		$this->config['phpbb_gallery_profile_pega'] = $option;
		$user_ids = array($user_id);
		$event_data = array('user_ids');
		$event_one = new \phpbb\event\data(compact($event_data));
		$profile_row = array();
		$event_two_data = array('profile_row');
		$event_two = new \phpbb\event\data(compact($event_two_data));
		$this->set_listener();
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.grab_profile_fields_data', array($this->listener, 'get_user_ids'));
		$dispatcher->addListener('core.generate_profile_fields_template_data_before', array($this->listener, 'profile_fileds'));
		$dispatcher->dispatch('core.grab_profile_fields_data', $event_one);
		$dispatcher->dispatch('core.generate_profile_fields_template_data_before', $event_two);

		$ouput = $event_two->get_data_filtered($event_two_data);
		$expacted = array(
			'phpbb_gallery'	=> array(
				'value'	=> array('album_id'	=> 1),
				'data'	=> array(
					'field_id'				=> '',
					'lang_id'				=> '',
					'lang_name'				=> 'GALLERY',
					'lang_explain'			=> '',
					'lang_default_value'	=> '',
					'field_name'			=> 'phpbb_gallery',
					'field_type'			=> 'profilefields.type.url',
					'field_ident'			=> 'phpbb_gallery',
					'field_length'			=> '40',
					'field_minlen'			=> '12',
					'field_maxlen'			=> '255',
					'field_novalue'			=> '',
					'field_default_value'	=> '',
					'field_validation'		=> '',
					'field_required'		=> '0',
					'field_show_on_reg'		=> '0',
					'field_hide'			=> '0',
					'field_no_view'			=> '0',
					'field_active'			=> '1',
					'field_order'			=> '',
					'field_show_profile'	=> '1',
					'field_show_on_vt'		=> '1',
					'field_show_novalue'	=> '0',
					'field_show_on_pm'		=> '1',
					'field_show_on_ml'		=> '1',
					'field_is_contact'		=> '1',
					'field_contact_desc'	=> 'VISIT_GALLERY',
					'field_contact_url'		=> '%s',
				)
			)
		);
		if ($expect)
		{
			$this->assertEquals($expacted, $ouput['profile_row']);
		}
		else
		{
			$this->assertEmpty($ouput['profile_row']);
		}
	}
}
