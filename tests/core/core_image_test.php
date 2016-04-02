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

class core_image_test extends core_base
{
	public function setUp()
	{
		parent::setUp();

		$this->album = new \phpbbgallery\core\album\album(
			$this->db,
			$this->user,
			'phpbb_gallery_albums',
			'phpbb_gallery_watch',
			'phpbb_gallery_contests'
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
		$this->gallery_config = new \phpbbgallery\core\config(
			$this->config
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
			$this->album,
			$this->template,
			$this->controller_helper,
			$this->gallery_config,
			$this->pagination,
			$this->notification_helper,
			'phpbb_gallery_images',
			'phpbb_gallery_reports'
		);
		$this->file = $this->getMockBuilder('\phpbbgallery\core\file\file')
			->disableOriginalConstructor()
			->getMock();
		$this->image = new \phpbbgallery\core\image\image(
			$this->db,
			$this->user,
			$this->template,
			$this->dispatcher,
			$this->gallery_auth,
			$this->album,
			$this->gallery_config,
			$this->controller_helper,
			$this->url,
			$this->log,
			$this->notification_helper,
			$this->report,
			$this->gallery_cache,
			$this->gallery_user,
			$this->file,
			'phpbb_gallery_image'
		);
	}
	
	public function test_get_status_orphan()
	{
		$this->assertEquals($this->image->get_status_orphan(), 3);
	}
	public function test_get_status_unaproved()
	{
		$this->assertEquals($this->image->get_status_unaproved(), 0);
	}
	public function test_get_status_aproved()
	{
		$this->assertEquals($this->image->get_status_aproved(), 1);
	}
	public function test_get_status_locked()
	{
		$this->assertEquals($this->image->get_status_locked(), 2);
	}
	public function test_get_no_contest()
	{
		$this->assertEquals($this->image->get_no_contest(), 0);
	}
	public function test_get_in_contest()
	{
		$this->assertEquals($this->image->get_in_contest(), 1);
	}

	/*
	 * TODO: Add tests for the other functions in this class
	 */
}