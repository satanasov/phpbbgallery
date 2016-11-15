<?php

/**
*
* PhpBB Gallery extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Lucifer <https://www.anavaro.com>
* @license GNU General Public License, version 2 (GPL-2.0)
* TO DO:
* Test Rating function
*
*/

namespace phpbbgallery\tests\core;
/**
* @group core
*/
require_once dirname(__FILE__) . '/../../../../includes/functions.php';

class core_search_test extends core_base
{
	public function setUp()
	{
		parent::setUp();
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
		$this->gallery_image = $this->getMockBuilder('\phpbbgallery\core\image\image')
			->disableOriginalConstructor()
			->getMock();
		$this->gallery_image->method('get_status_orphan')
			->willReturn(3);

		$this->gallery_config = new \phpbbgallery\core\config(
			$this->config
		);
		$this->block = new \phpbbgallery\core\block();
		$this->gallery_album = new \phpbbgallery\core\album\album(
			$this->db,
			$this->user,
			$this->gallery_auth,
			$this->gallery_cache,
			$this->block,
			$this->gallery_config,
			'phpbb_gallery_albums',
			'phpbb_gallery_images',
			'phpbb_gallery_watch',
			'phpbb_gallery_contests'
		);
		$this->url = new \phpbbgallery\core\url(
			$this->template,
			$this->request,
			$this->config,
			'/',
			'php'
		);
		$this->log = new \phpbbgallery\core\log(
			$this->db,
			$this->user,
			$this->user_loader,
			$this->template,
			$this->controller_helper,
			$this->pagination,
			$this->gallery_auth,
			$this->gallery_config,
			'phpbb_gallery_log'
		);
		$this->notification_helper = $this->getMockBuilder('\phpbbgallery\core\notification\helper')
			->disableOriginalConstructor()
			->getMock();

		$this->report = new \phpbbgallery\core\report(
			$this->log,
			$this->gallery_auth,
			$this->user,
			$this->db,
			$this->user_loader,
			$this->gallery_album,
			$this->template,
			$this->controller_helper,
			$this->gallery_config,
			$this->pagination,
			$this->notification_helper,
			'phpbb_gallery_images',
			'phpbb_gallery_reports'
		);

		$this->file = new \phpbbgallery\core\file\file(
			$this->request,
			$this->url,
			$this->gallery_config,
			2
		);
		$this->contest = new \phpbbgallery\core\contest(
			$this->db,
			$this->gallery_config,
			'phpbb_gallery_images',
			'phpbb_gallery_contests'
		);
		$this->gallery_image = new \phpbbgallery\core\image\image(
			$this->db,
			$this->user,
			$this->template,
			$this->dispatcher,
			$this->gallery_auth,
			$this->gallery_album,
			$this->gallery_config,
			$this->controller_helper,
			$this->url,
			$this->log,
			$this->notification_helper,
			$this->report,
			$this->gallery_cache,
			$this->gallery_user,
			$this->contest,
			$this->file,
			'phpbb_gallery_image'
		);

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

	// TEST RRC GENERATION!
	/**
	* Test data for Random image
	*
	* @return array Test DATA!
	*/
	public function random_test_data()
	{
		return array(
			'admin_all'	=> array(
				2, // User ID
				5, // User group
				10, // Limit
				0, // Search User
				7 // Expected
			),
			'admin_self'	=> array(
				2, // User ID
				5, // User group
				10, // Limit
				2, // Search User
				4 // Expected
			),
			'admin_limit'	=> array(
				2, // User ID
				5, // User group
				1, // Limit
				0, // Search User
				2 // Expected
			),
			'admin_user'	=> array(
				2, // User ID
				5, // User group
				10, // Limit
				52, // Search User
				3 // Expected
			),
			'user'	=> array(
				52, // User ID
				2, // User group
				10, // Limit
				0, // Search User
				4 // Expected
			),
			'user_admin'	=> array(
				52, // User ID
				2, // User group
				10, // Limit
				2, // Search User
				3 // Expected
			),
			'user_self'	=> array(
				52, // User ID
				2, // User group
				10, // Limit
				52, // Search User
				2 // Expected
			),
		);
	}
	/**
	* Test random images function
	* @dataProvider random_test_data
	*/
	public function test_random($user_id, $group_id, $limit, $search_user, $expected)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group_id'] = $group_id;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly($expected))
			->method('assign_block_vars');
		$this->gallery_search->random($limit, $search_user, 'rrc_gindex_display', 'tandom');
	}
	/**
	* Test recent images function
	* @dataProvider random_test_data
	*/
	public function test_recent($user_id, $group_id, $limit, $search_user, $expected)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group_id'] = $group_id;
		$this->gallery_config->set('default_sort_dir', 'a');
		$this->gallery_config->set('default_sort_key', 't');
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly($expected))
			->method('assign_block_vars');
		$this->gallery_search->recent($limit, 0, $search_user, 'rrc_gindex_display', 'recent');
	}

	/**
	* TEST RRC POLAROID FOR PROFILE AND GALLERY INDEX
	* Tested
	* - Random images in gallery index
	* - Recent images in gallery index
	* - Random images in user profile
	* - Recent images is user profile
	*/

	/**
	* Test data for rrc_gindex_display
	*
	* @return array Test DATA!
	*/
	public function rrc_gindex_display_test_data()
	{
		return array(
			'255'	=> array(
				255, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => 'phpbbgallery_core_image',
					'UC_IMAGE_NAME' => 'TestImage5',
					'U_ALBUM' => 'phpbbgallery_core_album',
					'ALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'IMAGE_VIEWS' => 0,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => '<span class="username">Test2</span>',
					'TIME' => null,
					'S_RATINGS' => 10,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => 1,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
			'173'	=> array(
				173, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => 'phpbbgallery_core_image',
					'UC_IMAGE_NAME' => 'TestImage5',
					'U_ALBUM' => 'phpbbgallery_core_album',
					'ALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => '<span class="username">Test2</span>',
					'TIME' => null,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
		/*	'128'	=> array(
				128, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 6,
					'U_IMAGE' => false,
					'UC_IMAGE_NAME' => false,
					'U_ALBUM' => false,
					'ALBUM_NAME' => false,
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => false,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => '127.0.0.1',
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			), Skip 128 as auth witll not allow admin IDin */
			'64'	=> array(
				64, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => false,
					'UC_IMAGE_NAME' => false,
					'U_ALBUM' => false,
					'ALBUM_NAME' => false,
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => false,
					'S_RATINGS' => 10,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
			'32'	=> array(
				32, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => false,
					'UC_IMAGE_NAME' => false,
					'U_ALBUM' => false,
					'ALBUM_NAME' => false,
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => '<span class="username">Test2</span>',
					'TIME' => false,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
			'16'	=> array(
				16, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => false,
					'UC_IMAGE_NAME' => false,
					'U_ALBUM' => false,
					'ALBUM_NAME' => false,
					'IMAGE_VIEWS' => 0,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => false,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
			'8'	=> array(
				8, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => false,
					'UC_IMAGE_NAME' => false,
					'U_ALBUM' => false,
					'ALBUM_NAME' => false,
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => null,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
			'4'	=> array(
				4, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => 'phpbbgallery_core_image',
					'UC_IMAGE_NAME' => 'TestImage5',
					'U_ALBUM' => false,
					'ALBUM_NAME' => false,
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => false,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
			'2'	=> array(
				2, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => false,
					'UC_IMAGE_NAME' => false,
					'U_ALBUM' => false,
					'ALBUM_NAME' => false,
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => false,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => 1,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
			'1'	=> array(
				1, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => false,
					'UC_IMAGE_NAME' => false,
					'U_ALBUM' => 'phpbbgallery_core_album',
					'ALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => false,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
			'0'	=> array(
				0, //rrc_gindex_display state
				array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => false,
					'UC_IMAGE_NAME' => false,
					'U_ALBUM' => false,
					'ALBUM_NAME' => false,
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => false,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				)
			),
		);
	}
	/**
	* Test rrc_gindex_display and rrc_profile_display
	* @dataProvider rrc_gindex_display_test_data
	*/
	public function test_rrc_gindex_display_recent($state, $expect)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_config->set('rrc_gindex_display', $state);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('imageblock', array(
					'BLOCK_NAME' => 'recent',
					'U_BLOCK' => 'phpbbgallery_core_search_egosearch'
				)),
				array('imageblock.image', $expect)
			);
		$this->gallery_search->recent(1, 0, 53, 'rrc_gindex_display', 'recent');
	}
	/**
	* Test rrc_gindex_display and rrc_profile_display
	* @dataProvider rrc_gindex_display_test_data
	*/
	public function test_rrc_profile_display_recent($state, $expect)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_config->set('rrc_profile_display', $state);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('imageblock', array(
					'BLOCK_NAME' => 'recent',
					'U_BLOCK' => 'phpbbgallery_core_search_egosearch'
				)),
				array('imageblock.image', $expect)
			);
		$this->gallery_search->recent(1, 0, 53, 'rrc_profile_display', 'recent');
	}
	/**
	* Test rrc_gindex_display and rrc_profile_display
	* @dataProvider rrc_gindex_display_test_data
	*/
	public function test_rrc_gindex_display_random($state, $expect)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_config->set('rrc_gindex_display', $state);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('imageblock', array(
					'BLOCK_NAME' => 'random',
					'U_BLOCK' => 'phpbbgallery_core_search_random'
				)),
				array('imageblock.image', $expect)
			);
		$this->gallery_search->random(1, 53, 'rrc_gindex_display', 'random');
	}
	/**
	* Test rrc_gindex_display and rrc_profile_display
	* @dataProvider rrc_gindex_display_test_data
	*/
	public function test_rrc_profile_display_random($state, $expect)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_config->set('rrc_profile_display', $state);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('imageblock', array(
					'BLOCK_NAME' => 'random',
					'U_BLOCK' => 'phpbbgallery_core_search_random'
				)),
				array('imageblock.image', $expect)
			);
		$this->gallery_search->random(1, 53, 'rrc_profile_display', 'random');
	}


	/**
	* TEST LINK GENERATION FOR THUMBAIL AND IMAGE NAME
	* - link_thumbnail in recent()
	* - link_thumbnail in random()
	* + link_thumbnail in rating()
	* - link_image_name in recent()
	* - link_image_name in random()
	* + link_image_name in rating()
	*/
	/**
	* Provide data for link generation test
	*/
	public function link_image_name_data()
	{
		return array(
			'image_page' => array(
				'image_page', // Input
				'phpbbgallery_core_image', // expected
			),
			'image' => array(
				'image', // Input
				'phpbbgallery_core_image_file_source', // expected
			),
			'other' => array(
				'none',
				false
			)
		);
	}
	/**
	* Test link_thumbnail in recent
	* @dataProvider link_image_name_data
	*/
	public function test_link_thumbnail_recent($state, $expect)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_config->set('link_thumbnail', $state);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('imageblock', array(
					'BLOCK_NAME' => 'recent',
					'U_BLOCK' => 'phpbbgallery_core_search_egosearch'
				)),
				array('imageblock.image', array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => 'phpbbgallery_core_image',
					'UC_IMAGE_NAME' => 'TestImage5',
					'U_ALBUM' => 'phpbbgallery_core_album',
					'ALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => $expect,
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => '<span class="username">Test2</span>',
					'TIME' => null,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				))
			);
		$this->gallery_search->recent(1, 0, 53, 'rrc_gindex_display', 'recent');
	}
	/**
	* Test link_thumbnail in random
	* @dataProvider link_image_name_data
	*/
	public function test_link_thumbnail_random($state, $expect)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_config->set('link_thumbnail', $state);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('imageblock', array(
					'BLOCK_NAME' => 'random',
					'U_BLOCK' => 'phpbbgallery_core_search_random'
				)),
				array('imageblock.image', array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => 'phpbbgallery_core_image',
					'UC_IMAGE_NAME' => 'TestImage5',
					'U_ALBUM' => 'phpbbgallery_core_album',
					'ALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => $expect,
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => null,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				))
			);
		$this->gallery_search->random(1, 53, 'rrc_profile_display', 'random');
	}
	/**
	* Test link_image_name in recent
	* @dataProvider link_image_name_data
	*/
	public function test_link_image_name_recent($state, $expect)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_config->set('link_image_name', $state);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('imageblock', array(
					'BLOCK_NAME' => 'recent',
					'U_BLOCK' => 'phpbbgallery_core_search_egosearch'
				)),
				array('imageblock.image', array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => $expect,
					'UC_IMAGE_NAME' => 'TestImage5',
					'U_ALBUM' => 'phpbbgallery_core_album',
					'ALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => '<span class="username">Test2</span>',
					'TIME' => null,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				))
			);
		$this->gallery_search->recent(1, 0, 53, 'rrc_gindex_display', 'recent');
	}
	/**
	* Test link_image_name in random
	* @dataProvider link_image_name_data
	*/
	public function test_link_image_name_random($state, $expect)
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_config->set('link_image_name', $state);
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('imageblock', array(
					'BLOCK_NAME' => 'random',
					'U_BLOCK' => 'phpbbgallery_core_search_random'
				)),
				array('imageblock.image', array(
					'IMAGE_ID' => 5,
					'U_IMAGE' => $expect,
					'UC_IMAGE_NAME' => 'TestImage5',
					'U_ALBUM' => 'phpbbgallery_core_album',
					'ALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'IMAGE_VIEWS' => -1,
					'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
					'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
					'S_UNAPPROVED' => false,
					'S_LOCKED' => false,
					'S_REPORTED' => false,
					'POSTER' => false,
					'TIME' => null,
					'S_RATINGS' => false,
					'U_RATINGS' => 'phpbbgallery_core_image#rating',
					'L_COMMENTS' => null,
					'S_COMMENTS' => false,
					'U_COMMENTS' => 'phpbbgallery_core_image#comments',
					'U_USER_IP' => false,
					'S_IMAGE_REPORTED' => 0,
					'U_IMAGE_REPORTED' => '',
					'S_STATUS_APPROVED' => true,
					'S_STATUS_UNAPPROVED' => false,
					'S_STATUS_UNAPPROVED_ACTION' => '',
					'S_STATUS_LOCKED' => false,
					'U_REPORT' => '',
					'U_STATUS' => '',
					'L_STATUS' => null,
				))
			);
		$this->gallery_search->random(1, 53, 'rrc_profile_display', 'random');
	}
	// Test recent_count
	/**
	* Test data for Random image
	*
	* @return array Test DATA!
	*/
	public function recent_count_data()
	{
		return array(
			'admin_all'	=> array(
				2, // User ID
				5, // User group
				6 // Expected
			),
			'user'	=> array(
				52, // User ID
				2, // User group
				3 // Expected
			),
		);
	}
	/**
	* Test recent_count
	* @dataProvider recent_count_data
	*/
	public function test_recent_count($user_id, $group_id, $expected)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group_id'] = $group_id;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->assertEquals($this->gallery_search->recent_count(), $expected);
	}
	// Recent comments testing
	/**
	* Test recent comments
	*/
	public function test_recent_comments_admin()
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(6))
			->method('assign_block_vars')
			->withConsecutive(
				array('commentrow', array(
					'COMMENT_ID' => 6,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_6',
					'POST_AUTHOR_FULL'	=> 2,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 6!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage6</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage6"><img src="http://gallery/image/6/mini" alt="TestImage6" title="TestImage6" /></a>',
					'IMAGE_AUTHOR'	=> 52,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 5,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_5',
					'POST_AUTHOR_FULL'	=> 2,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 5!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage5</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage5"><img src="http://gallery/image/5/mini" alt="TestImage5" title="TestImage5" /></a>',
					'IMAGE_AUTHOR'	=> 53,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 4,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_4',
					'POST_AUTHOR_FULL'	=> 2,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 4!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage4</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage4"><img src="http://gallery/image/4/mini" alt="TestImage4" title="TestImage4" /></a>',
					'IMAGE_AUTHOR'	=> 2,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 3,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_3',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 3!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage3</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage3"><img src="http://gallery/image/3/mini" alt="TestImage3" title="TestImage3" /></a>',
					'IMAGE_AUTHOR'	=> 52,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 2,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_2',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 2!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage2</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage2"><img src="http://gallery/image/2/mini" alt="TestImage2" title="TestImage2" /></a>',
					'IMAGE_AUTHOR'	=> 2,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 1,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_1',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 1!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage1</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage1"><img src="http://gallery/image/1/mini" alt="TestImage1" title="TestImage1" /></a>',
					'IMAGE_AUTHOR'	=> 2,
					'IMAGE_TIME'	=> null,
				))
			);
		$this->gallery_search->recent_comments(10);
	}
	public function test_recent_comments_admin_limit()
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('commentrow', array(
					'COMMENT_ID' => 6,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_6',
					'POST_AUTHOR_FULL'	=> 2,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 6!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage6</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage6"><img src="http://gallery/image/6/mini" alt="TestImage6" title="TestImage6" /></a>',
					'IMAGE_AUTHOR'	=> 52,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 5,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_5',
					'POST_AUTHOR_FULL'	=> 2,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 5!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage5</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage5"><img src="http://gallery/image/5/mini" alt="TestImage5" title="TestImage5" /></a>',
					'IMAGE_AUTHOR'	=> 53,
					'IMAGE_TIME'	=> null,
				))
			);
		$this->gallery_search->recent_comments(2);
	}
	public function test_recent_comments_admin_limit_start()
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('commentrow', array(
					'COMMENT_ID' => 3,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_3',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 3!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage3</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage3"><img src="http://gallery/image/3/mini" alt="TestImage3" title="TestImage3" /></a>',
					'IMAGE_AUTHOR'	=> 52,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 2,
					'U_DELETE'	=> 'phpbbgallery_core_comment_delete',
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_2',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 2!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage2</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage2"><img src="http://gallery/image/2/mini" alt="TestImage2" title="TestImage2" /></a>',
					'IMAGE_AUTHOR'	=> 2,
					'IMAGE_TIME'	=> null,
				))
			);
		$this->gallery_search->recent_comments(2,3);
	}
	public function test_recent_comments_admin_limit_start_overflow()
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(1))
			->method('assign_vars');
		$this->gallery_search->recent_comments(2,15);
	}
	public function test_recent_comments_user()
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 52;
		$this->user->data['group_id'] = 2;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(3))
			->method('assign_block_vars')
			->withConsecutive(
				array('commentrow', array(
					'COMMENT_ID' => 3,
					'U_DELETE'	=> false,
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_3',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 3!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage3</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage3"><img src="http://gallery/image/3/mini" alt="TestImage3" title="TestImage3" /></a>',
					'IMAGE_AUTHOR'	=> 52,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 2,
					'U_DELETE'	=> false,
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_2',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 2!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage2</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage2"><img src="http://gallery/image/2/mini" alt="TestImage2" title="TestImage2" /></a>',
					'IMAGE_AUTHOR'	=> 2,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 1,
					'U_DELETE'	=> false,
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_1',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 1!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage1</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage1"><img src="http://gallery/image/1/mini" alt="TestImage1" title="TestImage1" /></a>',
					'IMAGE_AUTHOR'	=> 2,
					'IMAGE_TIME'	=> null,
				))
			);
		$this->gallery_search->recent_comments(10);
	}
	public function test_recent_comments_user_limit()
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 52;
		$this->user->data['group_id'] = 2;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(1))
			->method('assign_block_vars')
			->withConsecutive(
				array('commentrow', array(
					'COMMENT_ID' => 3,
					'U_DELETE'	=> false,
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_3',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 3!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage3</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage3"><img src="http://gallery/image/3/mini" alt="TestImage3" title="TestImage3" /></a>',
					'IMAGE_AUTHOR'	=> 52,
					'IMAGE_TIME'	=> null,
				))
			);
		$this->gallery_search->recent_comments(1);
	}
	public function test_recent_comments_user_limit_start()
	{
		global $auth;
		$this->auth->method('get_acl')
			->willReturn(true);
		$auth = $this->auth;
		$this->user->data['user_id'] = 52;
		$this->user->data['group_id'] = 2;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('commentrow', array(
					'COMMENT_ID' => 2,
					'U_DELETE'	=> false,
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_2',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 2!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage2</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage2"><img src="http://gallery/image/2/mini" alt="TestImage2" title="TestImage2" /></a>',
					'IMAGE_AUTHOR'	=> 2,
					'IMAGE_TIME'	=> null,
				)),
				array('commentrow', array(
					'COMMENT_ID' => 1,
					'U_DELETE'	=> false,
					'U_EDIT' => 'phpbbgallery_core_comment_edit',
					'U_QUOTE'	=> 'phpbbgallery_core_comment_add',
					'U_COMMENT'	=> 'phpbbgallery_core_image#comment_1',
					'POST_AUTHOR_FULL'	=> 52,
					'TIME'	=> null,
					'TEXT'	=> 'This is test comment 1!!!!!',
					'UC_IMAGE_NAME'	=> '<a href="phpbbgallery_core_image">TestImage1</a>',
					'UC_THUMBNAIL'	=> '<a href="phpbbgallery_core_image" title="TestImage1"><img src="http://gallery/image/1/mini" alt="TestImage1" title="TestImage1" /></a>',
					'IMAGE_AUTHOR'	=> 2,
					'IMAGE_TIME'	=> null,
				))
			);
		$this->gallery_search->recent_comments(2, 1);
	}
}