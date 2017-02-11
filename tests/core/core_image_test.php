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

		$this->gallery_config = new \phpbbgallery\core\config(
			$this->config
		);

		$this->block = new \phpbbgallery\core\block();

		$this->album = new \phpbbgallery\core\album\album(
			$this->db,
			$this->user,
			$this->language,
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
			'phpBB/',
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
			'phpbb_gallery_log',
			'phpbb_gallery_images'
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
			$this->contest,
			$this->file,
			'phpbb_gallery_images'
		);
	}

	/*
	 * Provide data for get user_info
	 */
	public function data_get_new_author_info()
	{
		return array(
			'admin' => array(
				'admin', //request
				array(
					'username'	=> 'admin',
					'user_colour'	=> '',
					'user_id'		=> 2
				)
			),
			'none' => array(
				'blabla', //request
				false
			),
		);
	}

	/**
	 * Test get_new_author_info
	 * @dataProvider data_get_new_author_info
	 **/
	public function test_get_new_author_info($request, $expected)
	{
		$this->assertEquals($expected, $this->image->get_new_author_info($request));
	}


	/**
	 * TODO: Add tests for delete_images()
	 **/
	/**
	 * This is data for test_get_filenames
	 */
	public function data_get_filenames()
	{
		return array(
			'all_array' => array(
				array(1, 2, 3, 4, 5, 6), //Request
				array( // Response
					1	=> 'md5hashednamefor1.jpg',
					2	=> 'md5hashednamefor2.jpg',
					3	=> 'md5hashednamefor3.jpg',
					4	=> 'md5hashednamefor4.jpg',
					5	=> 'md5hashednamefor5.jpg',
					6	=> 'md5hashednamefor6.jpg'
				)
			),
			'single_array'	=> array(
				array(1), //Request
				array( // Response
					1	=> 'md5hashednamefor1.jpg'
				)
			),
			'single_int'	=> array(
				1, //Request
				array( // Response
					1	=> 'md5hashednamefor1.jpg'
				)
			),
			'invalid'	=> array(
				array(11), //Request
				array() // Respons
			)
		);
	}

	/**
	 * This tests the get_filenames
	 * @dataProvider data_get_filenames
	 */
	public function test_get_filenames($request, $expected)
	{
		$this->assertEquals($expected, $this->image->get_filenames($request));
	}

	/**
	 * TODO: Add test for generate_link
	 */

	/**
	 * TODO: Add test for handle_counter
	 */

	/**
	 * TODO: Add test for get_image_data
	 */

	/**
	 * TODO: Add test for approve_images
	 */

	/**
	 * TODO: Add test for unapprove_images
	 */

	/**
	 * TODO: Add test for move_image
	 */

	/**
	 * TODO: Add test for lock_image
	 */

	/**
	 * TODO: Add test for get_last_image
	 */

	/**
	 * Test for assign_block is done in tests\core\core_search_test
	 */
}