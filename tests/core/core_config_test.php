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

class core_config_test extends core_base
{
	public function setUp()
	{
		parent::setUp();
		$this->gallery_config = new \phpbbgallery\core\config(
			$this->config
		);
	}
	public function test_config_get_all()
	{
		$configs_array = array(
			'album_display'		=> 254,
			'album_images'		=> 2500,
			'allow_comments'	=> true,
			'allow_gif'			=> true,
			'allow_hotlinking'	=> true,
			'allow_jpg'			=> true,
			'allow_png'			=> true,
			'allow_rates'		=> true,
			'allow_resize'		=> true,
			'allow_rotate'		=> true,
			'allow_zip'			=> false,
			'captcha_comment'		=> true,
			'captcha_upload'		=> true,
			'comment_length'		=> 2000,
			'comment_user_control'	=> true,
			'contests_ended'		=> 0,
			'current_upload_dir_size'	=> 0,
			'current_upload_dir'	=> 0,
			'default_sort_dir'	=> 'd',
			'default_sort_key'	=> 't',
			'description_length'=> 2000,
			'disp_birthdays'			=> false,
			'disp_image_url'			=> true,
			'disp_login'				=> true,
			'disp_nextprev_thumbnail'	=> false,
			'disp_statistic'			=> true,
			'disp_total_images'			=> true,
			'disp_whoisonline'			=> true,
			'gdlib_version'		=> 2,
			'hotlinking_domains'	=> 'anavaro.com',
			'jpg_quality'			=> 100,
			'link_thumbnail'		=> 'image_page',
			'link_imagepage'		=> 'image',
			'link_image_name'		=> 'image_page',
			'link_image_icon'		=> 'image_page',
			'max_filesize'			=> 512000,
			'max_height'			=> 1024,
			'max_rating'			=> 10,
			'max_width'				=> 1280,
			'medium_cache'			=> true,
			'medium_height'			=> 600,
			'medium_width'			=> 800,
			'mini_thumbnail_disp'	=> true,
			'mini_thumbnail_size'	=> 70,
			'mvc_ignore'			=> 0,
			'mvc_time'				=> 0,
			'mvc_version'			=> '',
			'newest_pega_user_id'	=> 0,
			'newest_pega_username'	=> '',
			'newest_pega_user_colour'	=> '',
			'newest_pega_album_id'	=> 0,
			'num_comments'			=> 0,
			'num_images'			=> 0,
			'num_pegas'				=> 0,
			'num_uploads'			=> 10,
			'pegas_index_album'		=> false,
			//'pegas_index_random'	=> true,
			'pegas_index_rnd_count'	=> 4,
			//'pegas_index_recent'	=> true,
			'pegas_index_rct_count'	=> 4,
			'items_per_page'		=> 15,
			'profile_user_images'	=> true,
			'profile_pega'			=> true,
			'prune_orphan_time'		=> 0,
			'rrc_gindex_comments'	=> false,
			//'rrc_gindex_contests'	=> 1,
			'rrc_gindex_display'	=> 173,
			'rrc_gindex_mode'		=> 7,
			'rrc_gindex_pegas'		=> true,
			'rrc_profile_items'	=> 4,
			'rrc_profile_display'	=> 141,
			'rrc_profile_mode'		=> 3,
			//'rrc_profile_pegas'		=> true,
			'search_display'		=> 45,
			//'thumbnail_cache'		=> true,
			'thumbnail_height'		=> 160,
			//'thumbnail_infoline'	=> false,
			'thumbnail_quality'		=> 50,
			'thumbnail_width'		=> 240,
			'version'				=> '',
			//'viewtopic_icon'		=> true,
			//'viewtopic_images'		=> true,
			//'viewtopic_link'		=> false,
			'watermark_changed'		=> 0,
			'watermark_enabled'		=> true,
			'watermark_height'		=> 50,
			'watermark_position'	=> 20,
			'watermark_source'		=> 'gallery/images/watermark.png',
			'watermark_width'		=> 200,
		);
		$this->assertTrue($this->arrays_are_similar($configs_array, $this->gallery_config->get_all()));
	}
	// We should be able to get seted parts of config
	public function test_config_get_all_with_set()
	{
		$configs_array = array(
			'album_display'		=> 173,
			'album_images'		=> 2500,
			'allow_comments'	=> true,
			'allow_gif'			=> true,
			'allow_hotlinking'	=> true,
			'allow_jpg'			=> true,
			'allow_png'			=> true,
			'allow_rates'		=> true,
			'allow_resize'		=> true,
			'allow_rotate'		=> true,
			'allow_zip'			=> false,
			'captcha_comment'		=> true,
			'captcha_upload'		=> true,
			'comment_length'		=> 2000,
			'comment_user_control'	=> true,
			'contests_ended'		=> 0,
			'current_upload_dir_size'	=> 0,
			'current_upload_dir'	=> 0,
			'default_sort_dir'	=> 'd',
			'default_sort_key'	=> 't',
			'description_length'=> 2000,
			'disp_birthdays'			=> false,
			'disp_image_url'			=> true,
			'disp_login'				=> true,
			'disp_nextprev_thumbnail'	=> false,
			'disp_statistic'			=> true,
			'disp_total_images'			=> true,
			'disp_whoisonline'			=> true,
			'gdlib_version'		=> 2,
			'hotlinking_domains'	=> 'anavaro.com',
			'jpg_quality'			=> 100,
			'link_thumbnail'		=> 'image_page',
			'link_imagepage'		=> 'image',
			'link_image_name'		=> 'image_page',
			'link_image_icon'		=> 'image_page',
			'max_filesize'			=> 512000,
			'max_height'			=> 1024,
			'max_rating'			=> 10,
			'max_width'				=> 1280,
			'medium_cache'			=> true,
			'medium_height'			=> 600,
			'medium_width'			=> 800,
			'mini_thumbnail_disp'	=> true,
			'mini_thumbnail_size'	=> 70,
			'mvc_ignore'			=> 0,
			'mvc_time'				=> 0,
			'mvc_version'			=> '',
			'newest_pega_user_id'	=> 0,
			'newest_pega_username'	=> '',
			'newest_pega_user_colour'	=> '',
			'newest_pega_album_id'	=> 0,
			'num_comments'			=> 0,
			'num_images'			=> 0,
			'num_pegas'				=> 0,
			'num_uploads'			=> 10,
			'pegas_index_album'		=> false,
			//'pegas_index_random'	=> true,
			'pegas_index_rnd_count'	=> 4,
			//'pegas_index_recent'	=> true,
			'pegas_index_rct_count'	=> 4,
			'items_per_page'		=> 15,
			'profile_user_images'	=> true,
			'profile_pega'			=> true,
			'prune_orphan_time'		=> 0,
			'rrc_gindex_comments'	=> false,
			//'rrc_gindex_contests'	=> 1,
			'rrc_gindex_display'	=> 173,
			'rrc_gindex_mode'		=> 7,
			'rrc_gindex_pegas'		=> true,
			'rrc_profile_items'	=> 4,
			'rrc_profile_display'	=> 141,
			'rrc_profile_mode'		=> 3,
			//'rrc_profile_pegas'		=> true,
			'search_display'		=> 45,
			//'thumbnail_cache'		=> true,
			'thumbnail_height'		=> 160,
			//'thumbnail_infoline'	=> false,
			'thumbnail_quality'		=> 50,
			'thumbnail_width'		=> 240,
			'version'				=> '',
			//'viewtopic_icon'		=> true,
			//'viewtopic_images'		=> true,
			//'viewtopic_link'		=> false,
			'watermark_changed'		=> 0,
			'watermark_enabled'		=> true,
			'watermark_height'		=> 50,
			'watermark_position'	=> 20,
			'watermark_source'		=> 'gallery/images/watermark.png',
			'watermark_width'		=> 200,
		);
		$this->config['phpbb_gallery_album_display'] = 173;
		$this->assertTrue($this->arrays_are_similar($configs_array, $this->gallery_config->get_all()));
	}
	/**
	* Test data for the test_config_get test
	*
	* @return array Test data
	*/
	public function config_get_data()
	{
		return array(
			'get_existing_numeric' => array(
				'description_length', // request
				2000, //expect
				false // Should we destroy the object before requestin it
			),
			'get_existing_bool' => array(
				'allow_resize',
				1,
				false
			),
			'get_existing_string' => array(
				'link_thumbnail',
				'image_page',
				false
			),
			'get_non_existing_string' => array(
				'link_imagepage',
				'image',
				true
			),
			'get_non_existing_bool' => array(
				'allow_gif',
				1,
				true
			),
			'get_existing_numeric' => array(
				'album_images', // request
				2500, //expect
				true // Should we destroy the object before requestin it
			),
		);
	}
	/**
	* Test get function of config
	*
	* @dataProvider config_get_data
	*/
	public function test_config_get($variable, $expectation, $shenanigans)
	{
		if ($shenanigans)
		{
			$sql = 'DELETE FROM ' . CONFIG_TABLE . ' WHERE config_name = \'' . $variable . '\'';
			$this->db->sql_query($sql);
		}
		if (is_numeric($expectation))
		{
			$this->assertEquals($this->gallery_config->get($variable), $expectation);
		}
		else
		{
			$this->assertContains($this->gallery_config->get($variable), $expectation);
		}
	}
	
	/**
	* Test data for the test_config_set test
	*
	* @return array Test data
	*/
	public function config_set_data()
	{
		return array(
			'set_numeric' => array(
				'description_length', // Config name
				2000, // Old Value
				2500 // New value
			),
			'set_boolean' => array(
				'allow_resize', // Config name
				1, // Old Value
				0 // New value
			),
			'set_string' => array(
				'link_thumbnail',
				'image_page',
				'image'
			),
		);
	}
	/**
	* Test set function of config
	*
	* @dataProvider config_set_data
	*/
	public function test_config_set($variable, $old, $new)
	{
		if (is_numeric($old))
		{
			$this->assertEquals($this->gallery_config->get($variable), $old);
			$this->gallery_config->set($variable, $new);
			$this->assertEquals($this->gallery_config->get($variable), $new);
		}
		else
		{
			$this->assertContains($this->gallery_config->get($variable), $old);
			$this->gallery_config->set($variable, $new);
			$this->assertContains($this->gallery_config->get($variable), $new);
		}
	}
	public function test_config_inc()
	{
		$this->gallery_config->inc('description_length', 1);
		$this->gallery_config->inc('description_length', 1);
		$this->gallery_config->inc('description_length', 1);
		$this->assertEquals($this->config['phpbb_gallery_description_length'], 3);
	}
	public function test_config_dec()
	{
		$this->gallery_config->dec('description_length', 1);
		$this->gallery_config->dec('description_length', 1);
		$this->gallery_config->dec('description_length', 1);
		$this->assertEquals($this->config['phpbb_gallery_description_length'], -3);
	}
	/**
	 * Determine if two associative arrays are similar
	 *
	 * Both arrays must have the same indexes with identical values
	 * without respect to key ordering 
	 * 
	 * @param array $a
	 * @param array $b
	 * @return bool
	 * This is taken from stack overflow http://stackoverflow.com/questions/3838288/phpunit-assert-two-arrays-are-equal-but-order-of-elements-not-important
	 */
	function arrays_are_similar($a, $b) {
		// if the indexes don't match, return immediately
		if (count(array_diff_assoc($a, $b))) {
			return false;
		}
		// we know that the indexes, but maybe not values, match.
		// compare the values between the two arrays
		foreach($a as $k => $v) {
			if ($v !== $b[$k]) {
				return false;
			}
		}
		// we have identical indexes, and no unequal values
		return true;
	}
}