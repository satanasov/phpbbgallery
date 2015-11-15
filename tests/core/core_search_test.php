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
* @group core1
*/
require_once dirname(__FILE__) . '/../../../../includes/functions.php';

class core_search_test extends core_base
{
	public function setUp()
	{
		parent::setUp();
		$this->gallery_config = new \phpbbgallery\core\config(
			$this->config
		);

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

		$this->gallery_album = new \phpbbgallery\core\album\album(
			$this->db,
			$this->user,
			'phpbb_gallery_albums',
			'phpbb_gallery_watch',
			'phpbb_gallery_contests'
		);
		
		$this->gallery_image = $this->getMockBuilder('\phpbbgallery\core\image\image')
			->disableOriginalConstructor()
			->getMock();
			

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
	
	/**
	* Test random
	*/
	public function test_random()
	{
		$this->user->data['user_id'] = 2;
		$this->user->data['group_id'] = 5;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$this->gallery_search->random(15, 0, 'rrc_gindex_display', 'Random');
		$this->template->expects($this->once())
			->method('assign_block_vars');
	}
	// Dummy test
	public function test_dummy()
	{
		$this->assertEquals(11, 11);
	}
}