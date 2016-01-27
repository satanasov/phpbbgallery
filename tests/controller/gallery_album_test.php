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
* @group controller1
*/

require_once dirname(__FILE__) . '/../../../../includes/functions.php';
require_once dirname(__FILE__) . '/../../../../includes/functions_content.php';

class gallery_album_test extends controller_base
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
	
	public function get_controller($user_id, $grpup, $is_registered)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group'] = $group;
		$this->user->data['is_registered'] = $is_registered;
		$controller = new \phpbbgallery\core\controller\album(
			$this->config,
			$this->controller_helper,
			$this->db,
			$this->pagination,
			$this->template,
			$this->user,
			$this->display,
			$this->gallery_loader,
			$this->gallery_auth,
			$this->gallery_auth_level,
			$this->gallery_config,
			$this->gallery_notification_helper,
			$this->gallery_url,
			$this->gallery_image,
			$this->request,
			'phpbb_gallery_images'
		);
		
		return $controller;
	}

	public function test_for_base()
	{
		$this->template->expects($this->exactly(10))
			->method('assign_block_vars')
			->withConsecutive(
				array(
					'rules',
					array(
						'RULE' => null
					)
				),
				array(
					'rules',
					array(
						'RULE' => null
					)
				),
				array(
					'rules',
					array(
						'RULE' => null
					)
				),
				array(
					'rules',
					array(
						'RULE' => null
					)
				),
				array(
					'navlinks',
					array(
						'FORUM_NAME' => 'GALLERY',
						'U_VIEW_FORUM' => 'phpbbgallery_core_index'
					)
				),
				array(
					'navlinks',
					array(
						'FORUM_NAME' => 'TestPublicAlbum1',
						'FORUM_ID' => 1,
						'U_VIEW_FORUM' => 'phpbbgallery_core_album'
					)
				),
				array(
					'imageblock',
					array(
						'BLOCK_NAME' => 'TestPublicAlbum1'
					)
				),
				array(
					'imageblock.image',
					array(
						'IMAGE_ID' => 1,
						'U_IMAGE' => 'phpbbgallery_core_image',
						'UC_IMAGE_NAME' => 'TestImage1',
						'U_ALBUM' => false,
						'ALBUM_NAME' => false,
						'IMAGE_VIEWS' => 0,
						'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
						'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
						'S_UNAPPROVED' => false,
						'S_LOCKED' => false,
						'S_REPORTED' => false,
						'POSTER' => '<span class="username">admin</span>',
						'TIME' => null,
						'S_RATINGS' => false,
						'U_RATINGS' => 'phpbbgallery_core_image#rating',
						'L_COMMENTS' => null,
						'S_COMMENTS' => '',
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
				array(
					'imageblock.image',
					array(
						'IMAGE_ID' => 2,
						'U_IMAGE' => 'phpbbgallery_core_image',
						'UC_IMAGE_NAME' => 'TestImage2',
						'U_ALBUM' => false,
						'ALBUM_NAME' => false,
						'IMAGE_VIEWS' => 0,
						'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
						'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
						'S_UNAPPROVED' => false,
						'S_LOCKED' => false,
						'S_REPORTED' => false,
						'POSTER' => '<span class="username">admin</span>',
						'TIME' => null,
						'S_RATINGS' => false,
						'U_RATINGS' => 'phpbbgallery_core_image#rating',
						'L_COMMENTS' => null,
						'S_COMMENTS' => '',
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
				array(
					'imageblock.image',
					array(
						'IMAGE_ID' => 3,
						'U_IMAGE' => 'phpbbgallery_core_image',
						'UC_IMAGE_NAME' => 'TestImage3',
						'U_ALBUM' => false,
						'ALBUM_NAME' => false,
						'IMAGE_VIEWS' => 0,
						'UC_THUMBNAIL' => 'phpbbgallery_core_image_file_mini',
						'UC_THUMBNAIL_ACTION' => 'phpbbgallery_core_image',
						'S_UNAPPROVED' => false,
						'S_LOCKED' => false,
						'S_REPORTED' => false,
						'POSTER' => '<span class="username">testuser</span>',
						'TIME' => null,
						'S_RATINGS' => false,
						'U_RATINGS' => 'phpbbgallery_core_image#rating',
						'L_COMMENTS' => null,
						'S_COMMENTS' => '',
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
				)
			);
		$this->template->expects($this->exactly(4))
			->method('assign_vars')
			->withConsecutive(
				array(
					array(
						'ALBUM_ID' => 1,
						'ALBUM_NAME' => 'TestPublicAlbum1',
						'ALBUM_DESC' => '',
						'U_VIEW_ALBUM' => 'phpbbgallery_core_album',
					)
				),
				array(
					array(
						'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
						'S_HAS_SUBALBUM' => false,
						'L_SUBFORUM' => 'SUBALBUMS',
						'LAST_POST_IMG' => null,
						'FAKE_THUMB_SIZE' => ''

					)
				),
				array(
					array(
						'S_IS_POSTABLE' => true,
						'S_IS_LOCKED' => false,
						'U_RETURN_LINK' => 'phpbbgallery_core_index',
						'L_RETURN_LINK' => 'RETURN_TO_GALLERY',
						'S_ALBUM_ACTION' => 'phpbbgallery_core_album',
						'S_IS_WATCHED' => false,
						'U_WATCH_TOGLE' => 'phpbbgallery_core_album_watch'
					)
				),
				array(
					array(
						'TOTAL_IMAGES' => 'VIEW_ALBUM_IMAGES',
						'S_SELECT_SORT_DIR' => '<select name="sd" id="sd"><option value="a" selected="selected"></option><option value="d"></option></select>',
						'S_SELECT_SORT_KEY' => '<select name="sk" id="sk"><option value="t" selected="selected"></option><option value="n"></option><option value="vc"></option><option value="u"></option></select>'
					)
				)
			);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->base(1);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}
	protected function tearDown()
	{
		parent::tearDown();
	}
}
