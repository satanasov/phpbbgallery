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

class gallery_index_test extends controller_base
{
	/**
	* Setup test environment
	*/
	public function setUp()
	{

		parent::setUp();

		global $phpbb_dispatcher, $auth, $user, $cache, $db;

		$phpbb_dispatcher = $this->dispatcher;

		$auth = $this->auth;

		$user = $this->user;

		$cache = $this->cache;

		$db = $this->db;

	}

	// Add external function
	public function create_datetime_callback($time = 'now', \DateTimeZone $timezone = null)
	{
		$timezone = $timezone ?: $this->user->timezone;
		return new \phpbb\datetime($this->user, $time, $timezone);
	}

	public function test_install()
	{
		$db_tools = new \phpbb\db\tools\tools($this->db);
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_albums'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_albums_track'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_comments'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_contests'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_favorites'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_images'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_modscache'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_permissions'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_rates'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_reports'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_roles'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_users'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_watch'));
		$this->assertTrue($db_tools->sql_table_exists('phpbb_gallery_log'));
	}

	public function get_controller($user_id, $group, $is_registered)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group'] = $group;
		$this->user->data['is_registered'] = $is_registered;
		$controller = new \phpbbgallery\core\controller\index(
			$this->auth,
			$this->config,
			$this->db,
			$this->request,
			$this->template,
			$this->user,
			$this->controller_helper,
			$this->display,
			$this->gallery_config,
			$this->gallery_auth,
			$this->gallery_search,
			$this->pagination,
			$this->gallery_user,
			$this->gallery_image,
			'./',
			'php'
		);

		return $controller;
	}

	/**
	* Test controller index
	* function base()
	* As I can't pass parameters to ->withConsecutive but I want to
	* will have to make diferent test for each case
	*
	*/
	public function test_controller_base_case_1()
	{
		$this->template->expects($this->exactly(3))
			->method('assign_block_vars')
			->withConsecutive(
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => true,
					'ALBUM_ID' => 1,
					'ALBUM_NAME' => 'TestPublicAlbum1',
					'ALBUM_DESC' => '',
					'IMAGES' => 6,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read_subforum',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => null,
					'LAST_USER_FULL' => '<span class="username">admin</span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '<a href="phpbbgallery_core_album" class="subforum read" title="NO_NEW_IMAGES">TestPublicAlbumSubAlbum1</a>',
					'L_SUBALBUM_STR' => 'SUBALBUM',
					'L_ALBUM_FOLDER_ALT' => '',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('albumrow.subalbum', array(
					'U_SUBALBUM' => 'phpbbgallery_core_album',
					'SUBALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'S_UNREAD' => false,
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'GALLERY',
					'U_VIEW_FORUM' => 'phpbbgallery_core_index',
				))
			);
		$this->template->expects($this->exactly(6))
			->method('assign_vars')
			->withConsecutive(
				array(
					array(
						'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
						'S_HAS_SUBALBUM' => true,
						'L_SUBFORUM' => 'SUBALBUM',
						'LAST_POST_IMG' => null,
						'FAKE_THUMB_SIZE' => '',
					)
				),
				array(
					array(
						'S_USERS_PERSONAL_GALLERIES' => true,
						'U_USERS_PERSONAL_GALLERIES' => 'phpbbgallery_core_personal',
						'U_PERSONAL_GALLERIES_IMAGES' => 0,
						'U_PERSONAL_GALLERIES_LAST_IMAGE' => 'phpbbgallery_core_image_file_mini',
						'U_IMAGENAME' => false,
						'U_IMAGE_ACTION' => 'phpbbgallery_core_image',
						'U_IMAGENAME_ACTION' => 'phpbbgallery_core_image',
						'U_TIME' => false,
						'U_UPLOADER' => false
					)
				),
				array(
					array(
						'S_PERSONAL_ALBUM' => true,
						'U_PERSONAL_ALBUM' => 'phpbbgallery_core_album',
						'U_PERSONAL_ALBUM_USER' => null,
						'U_PERSONAL_ALBUM_COLOR' => null
					)
				),
				array(
					array(
						'LEGEND' => ''
					)
				),
				array(
					array(
						'TOTAL_IMAGES' => 'TOTAL_IMAGES_SPRINTF',
						'TOTAL_COMMENTS' => 'TOTAL_COMMENTS_SPRINTF',
						'TOTAL_PGALLERIES' => 'TOTAL_PEGAS_SPRINTF',
						'NEWEST_PGALLERIES' => ''
					)
				),
				array(
					array(
						'U_MCP' => 'phpbbgallery_core_moderate',
						'U_MARK_ALBUMS' => 'phpbbgallery_core_index',
						'S_LOGIN_ACTION' => './ucp.php?mode=login&amp;redirect=phpbbgallery_core_index',
						'U_GALLERY_SEARCH' => 'phpbbgallery_core_search',
						'U_G_SEARCH_COMMENTED' => false,
						'U_G_SEARCH_RECENT' => false,
						'U_G_SEARCH_RANDOM' => false,
						'U_G_SEARCH_SELF' => 'phpbbgallery_core_search_egosearch',
						'U_G_SEARCH_TOPRATED' => ''
					)
				)
			);
		$this->gallery_config->set('rrc_gindex_mode', 0);
		$this->gallery_config->set('link_image_icon', 'image_page');
		$this->gallery_config->set('pegas_index_album', 0);
		$this->gallery_config->set('disp_birthdays', 0);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->base();
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	/**
	* Case 2 -> show user albums
	* pegas_index_album -> 1
	*/
	public function test_controller_base_case_2()
	{
		$this->template->expects($this->exactly(5))
			->method('assign_block_vars')
			->withConsecutive(
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => true,
					'ALBUM_ID' => 1,
					'ALBUM_NAME' => 'TestPublicAlbum1',
					'ALBUM_DESC' => '',
					'IMAGES' => 6,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read_subforum',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => null,
					'LAST_USER_FULL' => '<span class="username">admin</span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '<a href="phpbbgallery_core_album" class="subforum read" title="NO_NEW_IMAGES">TestPublicAlbumSubAlbum1</a>',
					'L_SUBALBUM_STR' => 'SUBALBUM',
					'L_ALBUM_FOLDER_ALT' => '',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('albumrow.subalbum', array(
					'U_SUBALBUM' => 'phpbbgallery_core_album',
					'SUBALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'S_UNREAD' => false
				)),
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => false,
					'ALBUM_ID' => 4,
					'ALBUM_NAME' => 'TestUserAlbum1',
					'ALBUM_DESC' => '',
					'IMAGES' => 0,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => 0,
					'LAST_USER_FULL' => '<span class="username"></span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '',
					'L_SUBALBUM_STR' => '',
					'L_ALBUM_FOLDER_ALT' => 'NO_NEW_IMAGES',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => false,
					'ALBUM_ID' => 5,
					'ALBUM_NAME' => 'TestUserAlbum2',
					'ALBUM_DESC' => '',
					'IMAGES' => 0,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => 0,
					'LAST_USER_FULL' => '<span class="username"></span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '',
					'L_SUBALBUM_STR' => '',
					'L_ALBUM_FOLDER_ALT' => 'NO_NEW_IMAGES',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'GALLERY',
					'U_VIEW_FORUM' => 'phpbbgallery_core_index',
				))
			);
		$this->template->expects($this->exactly(5))
			->method('assign_vars')
			->withConsecutive(
				array(
					array(
						'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
						'S_HAS_SUBALBUM' => true,
						'L_SUBFORUM' => 'SUBALBUM',
						'LAST_POST_IMG' => null,
						'FAKE_THUMB_SIZE' => '',
					)
				),
				array(
					array(
						'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
						'S_HAS_SUBALBUM' => true,
						'L_SUBFORUM' => 'SUBALBUMS',
						'LAST_POST_IMG' => null,
						'FAKE_THUMB_SIZE' => '',
					)
				),
				array(
					array(
						'LEGEND' => ''
					)
				),
				array(
					array(
						'TOTAL_IMAGES' => 'TOTAL_IMAGES_SPRINTF',
						'TOTAL_COMMENTS' => 'TOTAL_COMMENTS_SPRINTF',
						'TOTAL_PGALLERIES' => 'TOTAL_PEGAS_SPRINTF',
						'NEWEST_PGALLERIES' => ''
					)
				),
				array(
					array(
						'U_MCP' => 'phpbbgallery_core_moderate',
						'U_MARK_ALBUMS' => 'phpbbgallery_core_index',
						'S_LOGIN_ACTION' => './ucp.php?mode=login&amp;redirect=phpbbgallery_core_index',
						'U_GALLERY_SEARCH' => 'phpbbgallery_core_search',
						'U_G_SEARCH_COMMENTED' => false,
						'U_G_SEARCH_RECENT' => false,
						'U_G_SEARCH_RANDOM' => false,
						'U_G_SEARCH_SELF' => 'phpbbgallery_core_search_egosearch',
						'U_G_SEARCH_TOPRATED' => ''
					)
				)
			);
		$this->gallery_config->set('link_image_icon', 'image');
		$this->gallery_config->set('pegas_index_album', 1);
		$this->gallery_config->set('rrc_gindex_mode', 0);
		$this->gallery_config->set('disp_birthdays', 0);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->base();
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	/**
	* Case 3 admin with display birthdays
	* disp_birthdays -> 1
	*/
	public function test_controller_base_case_3()
	{
		$this->template->expects($this->exactly(3))
			->method('assign_block_vars')
			->withConsecutive(
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => true,
					'ALBUM_ID' => 1,
					'ALBUM_NAME' => 'TestPublicAlbum1',
					'ALBUM_DESC' => '',
					'IMAGES' => 6,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read_subforum',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => null,
					'LAST_USER_FULL' => '<span class="username">admin</span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '<a href="phpbbgallery_core_album" class="subforum read" title="NO_NEW_IMAGES">TestPublicAlbumSubAlbum1</a>',
					'L_SUBALBUM_STR' => 'SUBALBUM',
					'L_ALBUM_FOLDER_ALT' => '',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('albumrow.subalbum', array(
					'U_SUBALBUM' => 'phpbbgallery_core_album',
					'SUBALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'S_UNREAD' => false,
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'GALLERY',
					'U_VIEW_FORUM' => 'phpbbgallery_core_index',
				))
			);
		$this->template->expects($this->exactly(7))
			->method('assign_vars')
			->withConsecutive(
				array(
					array(
						'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
						'S_HAS_SUBALBUM' => true,
						'L_SUBFORUM' => 'SUBALBUM',
						'LAST_POST_IMG' => null,
						'FAKE_THUMB_SIZE' => '',
					)
				),
				array(
					array(
						'S_USERS_PERSONAL_GALLERIES' => true,
						'U_USERS_PERSONAL_GALLERIES' => 'phpbbgallery_core_personal',
						'U_PERSONAL_GALLERIES_IMAGES' => 0,
						'U_PERSONAL_GALLERIES_LAST_IMAGE' => 'phpbbgallery_core_image_file_mini',
						'U_IMAGENAME' => false,
						'U_IMAGE_ACTION' => false,
						'U_IMAGENAME_ACTION' => 'phpbbgallery_core_image',
						'U_TIME' => false,
						'U_UPLOADER' => false
					)
				),
				array(
					array(
						'S_PERSONAL_ALBUM' => true,
						'U_PERSONAL_ALBUM' => 'phpbbgallery_core_album',
						'U_PERSONAL_ALBUM_USER' => null,
						'U_PERSONAL_ALBUM_COLOR' => null
					)
				),
				array(
					array(
						'LEGEND' => ''
					)
				),
				array(
					array(
						'S_DISPLAY_BIRTHDAY_LIST' => true
					)
				),
				array(
					array(
						'TOTAL_IMAGES' => 'TOTAL_IMAGES_SPRINTF',
						'TOTAL_COMMENTS' => 'TOTAL_COMMENTS_SPRINTF',
						'TOTAL_PGALLERIES' => 'TOTAL_PEGAS_SPRINTF',
						'NEWEST_PGALLERIES' => ''
					)
				),
				array(
					array(
						'U_MCP' => 'phpbbgallery_core_moderate',
						'U_MARK_ALBUMS' => 'phpbbgallery_core_index',
						'S_LOGIN_ACTION' => './ucp.php?mode=login&amp;redirect=phpbbgallery_core_index',
						'U_GALLERY_SEARCH' => 'phpbbgallery_core_search',
						'U_G_SEARCH_COMMENTED' => false,
						'U_G_SEARCH_RECENT' => false,
						'U_G_SEARCH_RANDOM' => false,
						'U_G_SEARCH_SELF' => 'phpbbgallery_core_search_egosearch',
						'U_G_SEARCH_TOPRATED' => ''
					)
				)
			);
		$this->gallery_config->set('rrc_gindex_mode', 0);
		$this->gallery_config->set('link_image_icon', 'none');
		$this->gallery_config->set('pegas_index_album', 0);
		$this->gallery_config->set('disp_birthdays', 1);
		$this->config->set('load_birthdays', 1);
		$this->config->set('allow_birthdays', 1);
		$this->auth->method('acl_gets')
			->willReturn(true);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->base();
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	/**
	* Test RRC Gallery Index Mode to show Recent images
	* rrc_ginde_mode -> 1
	*/
	public function test_controller_base_case_4()
	{
		$this->template->expects($this->exactly(4))
			->method('assign_block_vars')
			->withConsecutive(
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => true,
					'ALBUM_ID' => 1,
					'ALBUM_NAME' => 'TestPublicAlbum1',
					'ALBUM_DESC' => '',
					'IMAGES' => 6,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read_subforum',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => null,
					'LAST_USER_FULL' => '<span class="username">admin</span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '<a href="phpbbgallery_core_album" class="subforum read" title="NO_NEW_IMAGES">TestPublicAlbumSubAlbum1</a>',
					'L_SUBALBUM_STR' => 'SUBALBUM',
					'L_ALBUM_FOLDER_ALT' => '',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('albumrow.subalbum', array(
					'U_SUBALBUM' => 'phpbbgallery_core_album',
					'SUBALBUM_NAME' => 'TestPublicAlbumSubAlbum1',
					'S_UNREAD' => false,
				)),
				array('imageblock', array(
					'BLOCK_NAME' => null,
					'U_BLOCK' => 'phpbbgallery_core_search_recent'
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'GALLERY',
					'U_VIEW_FORUM' => 'phpbbgallery_core_index',
				))
			);
		$this->template->expects($this->exactly(8))
			->method('assign_vars')
			->withConsecutive(
				array(
					array(
						'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
						'S_HAS_SUBALBUM' => true,
						'L_SUBFORUM' => 'SUBALBUM',
						'LAST_POST_IMG' => null,
						'FAKE_THUMB_SIZE' => '',
					)
				),
				array(
					array(
						'S_USERS_PERSONAL_GALLERIES' => true,
						'U_USERS_PERSONAL_GALLERIES' => 'phpbbgallery_core_personal',
						'U_PERSONAL_GALLERIES_IMAGES' => 0,
						'U_PERSONAL_GALLERIES_LAST_IMAGE' => 'phpbbgallery_core_image_file_mini',
						'U_IMAGENAME' => false,
						'U_IMAGE_ACTION' => 'phpbbgallery_core_image',
						'U_IMAGENAME_ACTION' => 'phpbbgallery_core_image',
						'U_TIME' => false,
						'U_UPLOADER' => false
					)
				),
				array(
					array(
						'S_PERSONAL_ALBUM' => true,
						'U_PERSONAL_ALBUM' => 'phpbbgallery_core_album',
						'U_PERSONAL_ALBUM_USER' => null,
						'U_PERSONAL_ALBUM_COLOR' => null
					)
				),
				array(
					array(
						'U_RECENT' => true
					)
				),
				array(
					array(
						'TOTAL_IMAGES' => 'VIEW_ALBUM_IMAGES'
					)
				),
				array(
					array(
						'LEGEND' => ''
					)
				),
				array(
					array(
						'TOTAL_IMAGES' => 'TOTAL_IMAGES_SPRINTF',
						'TOTAL_COMMENTS' => 'TOTAL_COMMENTS_SPRINTF',
						'TOTAL_PGALLERIES' => 'TOTAL_PEGAS_SPRINTF',
						'NEWEST_PGALLERIES' => ''
					)
				),
				array(
					array(
						'U_MCP' => 'phpbbgallery_core_moderate',
						'U_MARK_ALBUMS' => 'phpbbgallery_core_index',
						'S_LOGIN_ACTION' => './ucp.php?mode=login&amp;redirect=phpbbgallery_core_index',
						'U_GALLERY_SEARCH' => 'phpbbgallery_core_search',
						'U_G_SEARCH_COMMENTED' => false,
						'U_G_SEARCH_RECENT' => 'phpbbgallery_core_search_recent',
						'U_G_SEARCH_RANDOM' => false,
						'U_G_SEARCH_SELF' => 'phpbbgallery_core_search_egosearch',
						'U_G_SEARCH_TOPRATED' => ''
					)
				)
			);
		$this->gallery_config->set('rrc_gindex_mode', 1);
		$this->gallery_config->set('pegas_index_rct_count', 1);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->base();
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}
	// Here we should have few more cases but let's move to personal albums
	/**
	* Test controller index
	* function personal()
	* As I can't pass parameters to ->withConsecutive but I want to
	* will have to make diferent test for each case
	* Default case -> admin see all personal albums with default per page
	*/
	public function test_index_personal_case_1()
	{
		$this->template->expects($this->exactly(4))
			->method('assign_block_vars')
			->withConsecutive(
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => false,
					'ALBUM_ID' => 4,
					'ALBUM_NAME' => 'TestUserAlbum1',
					'ALBUM_DESC' => '',
					'IMAGES' => 0,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => 0,
					'LAST_USER_FULL' => '<span class="username"></span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '',
					'L_SUBALBUM_STR' => '',
					'L_ALBUM_FOLDER_ALT' => 'NO_NEW_IMAGES',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => false,
					'ALBUM_ID' => 5,
					'ALBUM_NAME' => 'TestUserAlbum2',
					'ALBUM_DESC' => '',
					'IMAGES' => 0,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => 0,
					'LAST_USER_FULL' => '<span class="username"></span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '',
					'L_SUBALBUM_STR' => '',
					'L_ALBUM_FOLDER_ALT' => 'NO_NEW_IMAGES',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'GALLERY',
					'U_VIEW_FORUM' => 'phpbbgallery_core_index'
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'PERSONAL_ALBUMS',
					'U_VIEW_FORUM' => 'phpbbgallery_core_personal'
				))
			);
		$this->template->expects($this->exactly(3))
			->method('assign_vars')
			->withConsecutive(
				array(
					array(
						'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
						'S_HAS_SUBALBUM' => true,
						'L_SUBFORUM' => 'SUBALBUMS',
						'LAST_POST_IMG' => null,
						'FAKE_THUMB_SIZE' => ''
					)
				),
				array(
					array(
						'TOTAL_ALBUMS' => 'TOTAL_PEGAS_SHORT_SPRINTF_3'
					)
				),
				array(
					array(
						'S_CHAR_OPTIONS' => '<option value="" selected="selected">ALL</option><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="e">E</option><option value="f">F</option><option value="g">G</option><option value="h">H</option><option value="i">I</option><option value="j">J</option><option value="k">K</option><option value="l">L</option><option value="m">M</option><option value="n">N</option><option value="o">O</option><option value="p">P</option><option value="q">Q</option><option value="r">R</option><option value="s">S</option><option value="t">T</option><option value="u">U</option><option value="v">V</option><option value="w">W</option><option value="x">X</option><option value="y">Y</option><option value="z">Z</option><option value="other">#</option>'
					)
				)
			);
		$this->user->lang = array(
			'TOTAL_PEGAS_SHORT_SPRINTF'	=> array(
				'TOTAL_PEGAS_SHORT_SPRINTF_1',
				'TOTAL_PEGAS_SHORT_SPRINTF_2',
				'TOTAL_PEGAS_SHORT_SPRINTF_3'
			)
		);
		$this->gallery_config->set('link_image_icon', 'image');
		$this->gallery_config->set('pegas_index_album', 1);
		$this->gallery_config->set('rrc_gindex_mode', 0);
		$this->gallery_config->set('disp_birthdays', 0);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->personal(1);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	/**
	* Case 2 -> test pagination!
	*/
	public function test_index_personal_case_2()
	{
		$this->template->expects($this->exactly(3))
			->method('assign_block_vars')
			->withConsecutive(
				array('albumrow', array(
					'S_IS_CAT' => false,
					'S_NO_CAT' => false,
					'S_LOCKED_ALBUM' => false,
					'S_UNREAD_ALBUM' => false,
					'S_LIST_SUBALBUMS' => true,
					'S_SUBALBUMS' => false,
					'ALBUM_ID' => 5,
					'ALBUM_NAME' => 'TestUserAlbum2',
					'ALBUM_DESC' => '',
					'IMAGES' => 0,
					'UNAPPROVED_IMAGES' => 0,
					'ALBUM_IMG_STYLE' => 'forum_read',
					'ALBUM_FOLDER_IMG' => null,
					'ALBUM_FOLDER_IMG_ALT' => '',
					'LAST_IMAGE_TIME' => 0,
					'LAST_USER_FULL' => '<span class="username"></span>',
					'UC_THUMBNAIL' => '',
					'UC_FAKE_THUMBNAIL' => '',
					'UC_IMAGE_NAME' => '',
					'UC_LASTIMAGE_ICON' => '',
					'ALBUM_COLOUR' => '',
					'MODERATORS' => '',
					'SUBALBUMS' => '',
					'L_SUBALBUM_STR' => '',
					'L_ALBUM_FOLDER_ALT' => 'NO_NEW_IMAGES',
					'L_MODERATOR_STR' => '',
					'U_VIEWALBUM' => 'phpbbgallery_core_album'
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'GALLERY',
					'U_VIEW_FORUM' => 'phpbbgallery_core_index'
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'PERSONAL_ALBUMS',
					'U_VIEW_FORUM' => 'phpbbgallery_core_personal'
				))
			);
		$this->template->expects($this->exactly(3))
			->method('assign_vars')
			->withConsecutive(
				array(
					array(
						'U_MARK_ALBUMS' => 'phpbbgallery_core_album',
						'S_HAS_SUBALBUM' => true,
						'L_SUBFORUM' => 'SUBALBUMS',
						'LAST_POST_IMG' => null,
						'FAKE_THUMB_SIZE' => ''
					)
				),
				array(
					array(
						'TOTAL_ALBUMS' => 'TOTAL_PEGAS_SHORT_SPRINTF_3'
					)
				),
				array(
					array(
						'S_CHAR_OPTIONS' => '<option value="" selected="selected">ALL</option><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="e">E</option><option value="f">F</option><option value="g">G</option><option value="h">H</option><option value="i">I</option><option value="j">J</option><option value="k">K</option><option value="l">L</option><option value="m">M</option><option value="n">N</option><option value="o">O</option><option value="p">P</option><option value="q">Q</option><option value="r">R</option><option value="s">S</option><option value="t">T</option><option value="u">U</option><option value="v">V</option><option value="w">W</option><option value="x">X</option><option value="y">Y</option><option value="z">Z</option><option value="other">#</option>'
					)
				)
			);
		$this->user->lang = array(
			'TOTAL_PEGAS_SHORT_SPRINTF'	=> array(
				'TOTAL_PEGAS_SHORT_SPRINTF_1',
				'TOTAL_PEGAS_SHORT_SPRINTF_2',
				'TOTAL_PEGAS_SHORT_SPRINTF_3'
			)
		);
		$this->gallery_config->set('items_per_page', 1);
		$this->gallery_config->set('link_image_icon', 'image');
		$this->gallery_config->set('pegas_index_album', 1);
		$this->gallery_config->set('rrc_gindex_mode', 0);
		$this->gallery_config->set('disp_birthdays', 0);
		$controller = $this->get_controller(2, 5, true);
		$response = $controller->personal(2);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	/**
	* Case 3 -> User see no albums!
	*/
	public function test_index_personal_case_3()
	{
		$this->template->expects($this->exactly(2))
			->method('assign_block_vars')
			->withConsecutive(
				array('navlinks', array(
					'FORUM_NAME' => 'GALLERY',
					'U_VIEW_FORUM' => 'phpbbgallery_core_index'
				)),
				array('navlinks', array(
					'FORUM_NAME' => 'PERSONAL_ALBUMS',
					'U_VIEW_FORUM' => 'phpbbgallery_core_personal'
				))
			);
		$this->template->expects($this->exactly(3))
			->method('assign_vars')
			->withConsecutive(
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
						'TOTAL_ALBUMS' => 'TOTAL_PEGAS_SHORT_SPRINTF_3'
					)
				),
				array(
					array(
						'S_CHAR_OPTIONS' => '<option value="" selected="selected">ALL</option><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="e">E</option><option value="f">F</option><option value="g">G</option><option value="h">H</option><option value="i">I</option><option value="j">J</option><option value="k">K</option><option value="l">L</option><option value="m">M</option><option value="n">N</option><option value="o">O</option><option value="p">P</option><option value="q">Q</option><option value="r">R</option><option value="s">S</option><option value="t">T</option><option value="u">U</option><option value="v">V</option><option value="w">W</option><option value="x">X</option><option value="y">Y</option><option value="z">Z</option><option value="other">#</option>'
					)
				)
			);
		$this->user->lang = array(
			'TOTAL_PEGAS_SHORT_SPRINTF'	=> array(
				'TOTAL_PEGAS_SHORT_SPRINTF_1',
				'TOTAL_PEGAS_SHORT_SPRINTF_2',
				'TOTAL_PEGAS_SHORT_SPRINTF_3'
			)
		);
		$this->gallery_config->set('items_per_page', 1);
		$this->gallery_config->set('link_image_icon', 'image');
		$this->gallery_config->set('pegas_index_album', 1);
		$this->gallery_config->set('rrc_gindex_mode', 0);
		$this->gallery_config->set('disp_birthdays', 0);
		$controller = $this->get_controller(52, 2, true);
		$response = $controller->personal(1);
		$this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
		$this->assertEquals('200', $response->getStatusCode());
	}

	protected function tearDown()
	{
		parent::tearDown();
	}
}
