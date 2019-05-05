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

class core_cache_test extends core_base
{
	public function setUp()
	{
		parent::setUp();
		
		global $table_prefix;
		$table_prefix = 'phpbb_';
		$this->gallery_cache = new \phpbbgallery\core\cache(
			$this->cache,
			$this->db,
			'phpbb_gallery_albums',
			'phpbb_gallery_images'
		);
	}
	/**
	* Test get_albums function in cache
	* Test is disabled untill I find a way to make it not conflict with controller

	public function test_get_albums()
	{
		$test_array = array(
			'1'	=> array(
				'album_id'	=> 1,
				'parent_id'	=> 0,
				'album_name'	=> 'TestPublicAlbum1',
				'album_type'	=> 1,
				'left_id'	=> 0,
				'right_id'	=> 0,
				'album_user_id'	=> 0,
				'display_in_rrc'	=> true,
				'album_auth_access'	=> 0
			),
			'2'	=> array(
				'album_id'	=> 2,
				'parent_id'	=> 1,
				'album_name'	=> 'TestPublicAlbumSubAlbum1',
				'album_type'	=> 1,
				'left_id'	=> 1,
				'right_id'	=> 3,
				'album_user_id'	=> 0,
				'display_in_rrc'	=> false,
				'album_auth_access'	=> 0
			),
			'3'	=> array(
				'album_id'	=> 3,
				'parent_id'	=> 1,
				'album_name'	=> 'TestPublicAlbumSubAlbum2',
				'album_type'	=> 1,
				'left_id'	=> 1,
				'right_id'	=> 2,
				'album_user_id'	=> 0,
				'display_in_rrc'	=> true,
				'album_auth_access'	=> 0
			),
			'4'	=> array(
				'album_id'	=> 4,
				'parent_id'	=> 0,
				'album_name'	=> 'TestUserAlbum1',
				'album_type'	=> 1,
				'left_id'	=> 0,
				'right_id'	=> 0,
				'album_user_id'	=> 2,
				'display_in_rrc'	=> true,
				'album_auth_access'	=> 0
			)
		);
		// Let's first destroy the cached albums (controller makes some fun with them)
		$this->gallery_cache->destroy_albums();
		$this->assertEquals($test_array, $this->gallery_cache->get_albums());
	}*/
	/**
	* Test data for the test_get_images test
	*
	* @return array Test data
	*/
	public function data_get_images()
	{
		return array(
			'image_1' => array(
				array(1),
				array(
					1 => array(
						'image_id'				=> 1,
						'image_filename'		=> 'md5hashednamefor1.jpg',
						'image_name'			=> 'TestImage1',
						'image_name_clean'		=> 'testimage1',
						'image_desc'			=> '',
						'image_desc_uid'		=> '10fu4clu',
						'image_desc_bitfield'	=> '',
						'image_user_id'			=> 2,
						'image_username'		=> 'admin',
						'image_username_clean'	=> 'admin',
						'image_user_colour'		=> '',
						'image_user_ip'			=> '127.0.0.1',
						'image_time'			=> 0,
						'image_album_id'		=> 1,
						'image_view_count'		=> 0,
						'image_status'			=> 1,
						'image_filemissing'		=> 0,
						'image_rates'			=> 0,
						'image_rate_points'		=> 0,
						'image_rate_avg'		=> 0,
						'image_comments'		=> 1,
						'image_last_comment'	=> 1,
						'image_allow_comments'	=> 1,
						'image_favorited'		=> 0,
						'image_reported'		=> 0,
						'filesize_upload'		=> 0,
						'filesize_medium'		=> 0,
						'filesize_cache'		=> 0,
						'album_name'			=> 'TestPublicAlbum1',
					)
				)
			)
		);
	}
	/**
	* get_images
	*
	* @dataProvider data_get_images
	*/
	public function test_get_images($request, $expected)
	{
		$this->assertEquals($this->gallery_cache->get_images($request), $expected);
	}
}