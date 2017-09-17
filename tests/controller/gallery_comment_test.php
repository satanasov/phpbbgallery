<?php
/**
 * Created by PhpStorm.
 * User: lucifer
 * Date: 8.9.2017 г.
 * Time: 4:47
 */

namespace phpbbgallery\tests\controller;

/**
 * @group controller1
 */

class gallery_comment_test extends controller_base
{
	/** @var bool A return value for check_form_key() */
	public static $valid_form = true;

	/**
	 * Setup test environment
	 */
	public function setUp()
	{

		parent::setUp();

		global $phpbb_dispatcher, $auth, $user, $cache, $db, $request, $config, $template, $phpbb_container;

		$phpbb_dispatcher = $this->dispatcher;

		$auth = $this->auth;

		$user = $this->user;

		$cache = $this->cache;

		$db = $this->db;

		$request = $this->request;

		$config = $this->config;

		$template = $this->template;

		$phpbb_container = $this->phpbb_container;

	}

	public function get_controller($user_id, $group, $is_registered)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group'] = $group;
		$this->user->data['is_registered'] = $is_registered;
		$controller = new \phpbbgallery\core\controller\comment(
			$this->db,
			$this->user,
			$this->language,
			$this->auth,
			$this->config,
			$this->template,
			$this->request,
			$this->controller_helper,
			$this->gallery_image,
			$this->gallery_loader,
			$this->gallery_album,
			$this->display,
			$this->gallery_url,
			$this->gallery_auth,
			$this->gallery_config,
			$this->misc,
			$this->gallery_comment,
			$this->gallery_user,
			$this->log,
			$this->gallery_notification_helper,
			$this->gallery_notification,
			$this->gallery_rating,
			$this->phpbb_container,
			'phpbb_gallery_comments',
			'phpBB/',
			'php'
		);

		return $controller;
	}

	public function test_for_add_no_submit_no_comment()
	{
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
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
				)
			);
		$this->template->expects($this->exactly(3))
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
						'BBCODE_STATUS' => 'BBCODE_IS_OFF',
						'IMG_STATUS' => 'IMAGES_ARE_OFF',
						'FLASH_STATUS' => 'FLASH_IS_OFF',
						'SMILIES_STATUS' => 'SMILIES_ARE_OFF',
						'URL_STATUS' => 'URL_IS_OFF',
						'S_BBCODE_ALLOWED' => false,
						'S_SMILIES_ALLOWED' => false,
						'S_LINKS_ALLOWED' => false,
						'S_BBCODE_IMG' => false,
						'S_BBCODE_URL' => false,
						'S_BBCODE_FLASH' => false,
						'S_BBCODE_QUOTE' => true,

					)
				),
				array(
					array(
						'S_ALBUM_ACTION' => 'phpbbgallery_core_comment_add',
						'ERROR' => '',
						'MESSAGE' => '',
						'USERNAME' => '',
						'REQ_USERNAME' => false,
						'L_COMMENT_LENGTH' => 'COMMENT_LENGTH',
						'IMAGE_RSZ_WIDTH' => 800,
						'IMAGE_RSZ_HEIGHT' => 600,
						'U_IMAGE' => 'http://gallery/image/1/medium',
						'U_VIEW_IMAGE' => 'http://gallery/image/1',
						'IMAGE_NAME' => 'TestImage1',
						'S_SIGNATURE_CHECKED' => '',
					)
				)
			);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->add(1, 0);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}
	public function test_for_add_no_submit_with_comment()
	{
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
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
				)
			);
		$this->template->expects($this->exactly(3))
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
						'BBCODE_STATUS' => 'BBCODE_IS_OFF',
						'IMG_STATUS' => 'IMAGES_ARE_OFF',
						'FLASH_STATUS' => 'FLASH_IS_OFF',
						'SMILIES_STATUS' => 'SMILIES_ARE_OFF',
						'URL_STATUS' => 'URL_IS_OFF',
						'S_BBCODE_ALLOWED' => false,
						'S_SMILIES_ALLOWED' => false,
						'S_LINKS_ALLOWED' => false,
						'S_BBCODE_IMG' => false,
						'S_BBCODE_URL' => false,
						'S_BBCODE_FLASH' => false,
						'S_BBCODE_QUOTE' => true,

					)
				),
				array(
					array(
						'S_ALBUM_ACTION' => 'phpbbgallery_core_comment_add',
						'ERROR' => '',
						'MESSAGE' => '[quote=""]This is test comment 1!!!!![/quote]',
						'USERNAME' => '',
						'REQ_USERNAME' => false,
						'L_COMMENT_LENGTH' => 'COMMENT_LENGTH',
						'IMAGE_RSZ_WIDTH' => 800,
						'IMAGE_RSZ_HEIGHT' => 600,
						'U_IMAGE' => 'http://gallery/image/1/medium',
						'U_VIEW_IMAGE' => 'http://gallery/image/1',
						'IMAGE_NAME' => 'TestImage1',
						'S_SIGNATURE_CHECKED' => '',
					)
				)
			);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->add(1, 1);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}
	public function inact_test_for_add_do_submit_no_comment()
	{
		$this->request->method('variable')
			->willReturn(true);

		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
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
				)
			);
		$this->template->expects($this->exactly(3))
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
						'BBCODE_STATUS' => 'BBCODE_IS_OFF',
						'IMG_STATUS' => 'IMAGES_ARE_OFF',
						'FLASH_STATUS' => 'FLASH_IS_OFF',
						'SMILIES_STATUS' => 'SMILIES_ARE_OFF',
						'URL_STATUS' => 'URL_IS_OFF',
						'S_BBCODE_ALLOWED' => false,
						'S_SMILIES_ALLOWED' => false,
						'S_LINKS_ALLOWED' => false,
						'S_BBCODE_IMG' => false,
						'S_BBCODE_URL' => false,
						'S_BBCODE_FLASH' => false,
						'S_BBCODE_QUOTE' => true,

					)
				),
				array(
					array(
						'S_ALBUM_ACTION' => 'phpbbgallery_core_comment_add',
						'ERROR' => '',
						'MESSAGE' => '',
						'USERNAME' => '',
						'REQ_USERNAME' => false,
						'L_COMMENT_LENGTH' => 'COMMENT_LENGTH',
						'IMAGE_RSZ_WIDTH' => 800,
						'IMAGE_RSZ_HEIGHT' => 600,
						'U_IMAGE' => 'http://gallery/image/1/medium',
						'U_VIEW_IMAGE' => 'http://gallery/image/1',
						'IMAGE_NAME' => 'TestImage1',
						'S_SIGNATURE_CHECKED' => '',
					)
				)
			);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->add(1, 0);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}
}
