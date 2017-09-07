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
require_once dirname(__FILE__) . '/../../../../../includes/functions.php';

class core_display_test extends \phpbbgallery\tests\core\core_base
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

		$this->misc = $this->getMockBuilder('\phpbbgallery\core\misc')
			->disableOriginalConstructor()
			->getMock();

		$this->display = new \phpbbgallery\core\album\display(
			$this->auth,
			$this->config,
			$this->controller_helper,
			$this->db,
			$this->pagination,
			$this->request,
			$this->template,
			$this->user,
			$this->language,
			$this->gallery_auth,
			$this->gallery_user,
			$this->misc,
			'/',
			'php',
			'phpbb_gallery_albums',
			'phpbb_gallery_contests',
			'phpbb_gallery_albums_track',
			'phpbb_gallery_albums_modscache'
		);
	}
	/**
	 * Test data for the test_get_branch test
	 *
	 * @return array Test data
	 */
	public function get_branch_data()
	{
		return array(
			'album1_default'	=> array(
				0, // branch user id
				1, // album_id,
				'all', // Type
				'descending', // Order
				true, // Include album
				array( // expected
					0 => Array (
						'album_id' => '1',
						'parent_id' => '0',
						'left_id' => '0',
						'right_id' => '0',
						'album_parents' => '',
						'album_type' => '1',
						'album_status' => '0',
						'album_contest' => '0',
						'album_name' => 'TestPublicAlbum1',
						'album_desc' => '',
						'album_desc_options' => '7',
						'album_desc_uid' => '',
						'album_desc_bitfield' => '',
						'album_user_id' => '0',
						'album_images' => '3',
						'album_images_real' => '3',
						'album_last_image_id' => '3',
						'album_image' => '',
						'album_last_image_time' => '0',
						'album_last_image_name' => 'TestImage3',
						'album_last_username' => 'admin',
						'album_last_user_colour' => '',
						'album_last_user_id' => '2',
						'album_watermark' => '1',
						'album_sort_key' => '',
						'album_sort_dir' => '',
						'display_in_rrc' => '1',
						'display_on_index' => '1',
						'display_subalbum_list' => '1',
						'album_feed' => '1',
						'album_auth_access' => '0',
					)
				)
			),
			'album1_children'	=> array(
				0, // branch user id
				1, // album_id,
				'children', // Type
				'descending', // Order
				true, // Include album
				array( // expected
					0 => Array (
						'album_id' => '1',
						'parent_id' => '0',
						'left_id' => '0',
						'right_id' => '0',
						'album_parents' => '',
						'album_type' => '1',
						'album_status' => '0',
						'album_contest' => '0',
						'album_name' => 'TestPublicAlbum1',
						'album_desc' => '',
						'album_desc_options' => '7',
						'album_desc_uid' => '',
						'album_desc_bitfield' => '',
						'album_user_id' => '0',
						'album_images' => '3',
						'album_images_real' => '3',
						'album_last_image_id' => '3',
						'album_image' => '',
						'album_last_image_time' => '0',
						'album_last_image_name' => 'TestImage3',
						'album_last_username' => 'admin',
						'album_last_user_colour' => '',
						'album_last_user_id' => '2',
						'album_watermark' => '1',
						'album_sort_key' => '',
						'album_sort_dir' => '',
						'display_in_rrc' => '1',
						'display_on_index' => '1',
						'display_subalbum_list' => '1',
						'album_feed' => '1',
						'album_auth_access' => '0',
					)
				)
			),
			'album2'	=> array(
				0, // branch user id
				2, // album_id,
				'all', // Type
				'descending', // Order
				true, // Include album
				array( // expected
					0	=> array(
						'album_id' => '2',
						'parent_id' => '1',
						'left_id' => '1',
						'right_id' => '3',
						'album_parents' => 'a:1:{i:1;a:2:{i:0;s:8:"TestPublicAlbum1";i:1;i:1;}}',
						'album_type' => '1',
						'album_status' => '0',
						'album_contest' => '0',
						'album_name' => 'TestPublicAlbumSubAlbum1',
						'album_desc' => '',
						'album_desc_options' => '7',
						'album_desc_uid' => '',
						'album_desc_bitfield' => '',
						'album_user_id' => '0',
						'album_images' => '3',
						'album_images_real' => '3',
						'album_last_image_id' => '6',
						'album_image' => '',
						'album_last_image_time' => '0',
						'album_last_image_name' => 'TestImage6',
						'album_last_username' => 'admin',
						'album_last_user_colour' => '',
						'album_last_user_id' => '2',
						'album_watermark' => '1',
						'album_sort_key' => '',
						'album_sort_dir' => '',
						'display_in_rrc' => '0',
						'display_on_index' => '1',
						'display_subalbum_list' => '1',
						'album_feed' => '1',
						'album_auth_access' => '0',
					),
					1	=> array(
						'album_id' => '3',
						'parent_id' => '1',
						'left_id' => '1',
						'right_id' => '2',
						'album_parents' => 'a:1:{i:1;a:2:{i:0;s:8:"TestPublicAlbum1";i:1;i:1;}}',
						'album_type' => '1',
						'album_status' => '0',
						'album_contest' => '0',
						'album_name' => 'TestPublicAlbumSubAlbum2',
						'album_desc' => '',
						'album_desc_options' => '7',
						'album_desc_uid' => '',
						'album_desc_bitfield' => '',
						'album_user_id' => '0',
						'album_images' => '3',
						'album_images_real' => '3',
						'album_last_image_id' => '9',
						'album_image' => '',
						'album_last_image_time' => '0',
						'album_last_image_name' => 'TestImage9',
						'album_last_username' => 'admin',
						'album_last_user_colour' => '',
						'album_last_user_id' => '2',
						'album_watermark' => '1',
						'album_sort_key' => '',
						'album_sort_dir' => '',
						'display_in_rrc' => '1',
						'display_on_index' => '1',
						'display_subalbum_list' => '1',
						'album_feed' => '1',
						'album_auth_access' => '0',
					)
				)
			),
			'album2_revert_no_album'	=> array(
				0, // branch user id
				2, // album_id,
				'all', // Type
				'asc', // Order
				false, // Include album
				array( // expected
					0	=> array(
						'album_id' => '3',
						'parent_id' => '1',
						'left_id' => '1',
						'right_id' => '2',
						'album_parents' => 'a:1:{i:1;a:2:{i:0;s:8:"TestPublicAlbum1";i:1;i:1;}}',
						'album_type' => '1',
						'album_status' => '0',
						'album_contest' => '0',
						'album_name' => 'TestPublicAlbumSubAlbum2',
						'album_desc' => '',
						'album_desc_options' => '7',
						'album_desc_uid' => '',
						'album_desc_bitfield' => '',
						'album_user_id' => '0',
						'album_images' => '3',
						'album_images_real' => '3',
						'album_last_image_id' => '9',
						'album_image' => '',
						'album_last_image_time' => '0',
						'album_last_image_name' => 'TestImage9',
						'album_last_username' => 'admin',
						'album_last_user_colour' => '',
						'album_last_user_id' => '2',
						'album_watermark' => '1',
						'album_sort_key' => '',
						'album_sort_dir' => '',
						'display_in_rrc' => '1',
						'display_on_index' => '1',
						'display_subalbum_list' => '1',
						'album_feed' => '1',
						'album_auth_access' => '0',
					)
				)
			)
		);
	}
	/**
	 * test_get_branch
	 *
	 * @dataProvider get_branch_data
	 */
	public function test_get_branch($branch_user_id, $album_id, $type, $order, $include_album, $expected)
	{
		$this->assertEquals($expected, $this->display->get_branch($branch_user_id, $album_id, $type, $order, $include_album));
	}
	
	
}