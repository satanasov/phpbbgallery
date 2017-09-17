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

class gallery_moderate_test extends controller_base
{
	/**
	* Setup test environment
	*/
	public function setUp()
	{

		parent::setUp();

		global $phpbb_dispatcher, $auth, $user, $cache, $db, $request;

		$phpbb_dispatcher = $this->dispatcher;

		$auth = $this->auth;

		$user = $this->user;

		$cache = $this->cache;

		$db = $this->db;

		$request = $this->request;

	}

	public function get_controller($user_id, $group, $is_registered)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group'] = $group;
		$this->user->data['is_registered'] = $is_registered;
		$controller = new \phpbbgallery\core\controller\moderate(
			$this->config,
			$this->request,
			$this->template,
			$this->user,
			$this->language,
			$this->controller_helper,
			$this->display,
			$this->gallery_moderate,
			$this->gallery_auth,
			$this->misc,
			$this->gallery_album,
			$this->gallery_image,
			$this->gallery_notification_helper,
			$this->gallery_url,
			$this->log,
			$this->gallery_report,
			$this->user_loader,
			'/',
			'php'
		);

		return $controller;
	}

	/**
	 * Provide data for test_for_base
	 */
	public function for_base_data()
	{
		return	array(
			'base' => array(
				2, // user_id
				5, // group_id
				true, //is_registered
				0, // album
				array( // menu
					'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
					'S_HAS_SUBALBUM' => true,
					'L_SUBFORUM' => 'SUBALBUM',
					'LAST_POST_IMG' => null,
					'FAKE_THUMB_SIZE' => '',
				),
				array(
					'S_HAS_LOGS' => false,
					'TOTAL_PAGES' => 'PAGE_TITLE_NUMBER'
				),
				array( //expected
					'U_GALLERY_MODERATE_OVERVIEW'	=> 'phpbbgallery_core_moderate',
					'U_GALLERY_MODERATE_APPROVE'	=> 'phpbbgallery_core_moderate_queue_approve',
					'U_GALLERY_MODERATE_REPORT'		=> 'phpbbgallery_core_moderate_reports',
					'U_ALBUM_OVERVIEW'				=> false,
					'U_GALLERY_MCP_LOGS'			=> 'phpbbgallery_core_moderate_action_log',
					'U_ALBUM_NAME'					=> false,
					'U_OVERVIEW'					=> true,
				)
			),
			'user' => array(
				53, // user_id
				2, // group_id
				true, //is_registered
				0, // album
				array( // menu
					'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
					'S_HAS_SUBALBUM' => false,
					'L_SUBFORUM' => 'SUBALBUMS',
					'LAST_POST_IMG' => null,
					'FAKE_THUMB_SIZE' => '',
				),
				array(
					'S_HAS_LOGS' => false,
					'TOTAL_PAGES' => 'PAGE_TITLE_NUMBER'
				),
				array( //expected
					'U_GALLERY_MODERATE_OVERVIEW'	=> 'phpbbgallery_core_moderate',
					'U_GALLERY_MODERATE_APPROVE'	=> 'phpbbgallery_core_moderate_queue_approve',
					'U_GALLERY_MODERATE_REPORT'		=> 'phpbbgallery_core_moderate_reports',
					'U_ALBUM_OVERVIEW'				=> false,
					'U_GALLERY_MCP_LOGS'			=> 'phpbbgallery_core_moderate_action_log',
					'U_ALBUM_NAME'					=> false,
					'U_OVERVIEW'					=> true,
				)
			)
		);
	}
	/**
	 * Test base case scenario - User is admin and has not given any album
	 * @dataProvider for_base_data
	 */
	public function test_for_base($user_id, $group_id, $is_registered, $album, $menu, $populate, $expected)
	{
		$this->template->expects($this->exactly(3))
			->method('assign_vars')
			->withConsecutive(
				array(
					$menu
				),
				array(
					$populate
				),
				array(
					$expected
				)
			);
		$controller = $this->get_controller($user_id, $group_id, $is_registered);
		$response = $controller->base($album);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}
	protected function tearDown()
	{
		parent::tearDown();
	}
}
