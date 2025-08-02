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
* @group core_dev
*/
require_once dirname(__FILE__) . '/../../../../includes/functions.php';

class core_rating_test extends core_base
{
	protected $gallery_cache;
	protected $gallery_user;
	protected $gallery_auth;
	protected $gallery_rating;

	public function setUp() : void
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
			$this->user_cpf,
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

		$this->gallery_rating = new \phpbbgallery\core\rating(
			$this->db,
			$this->template,
			$this->user,
			$this->language,
			$this->request,
			$this->gallery_config,
			$this->gallery_auth,
			'phpbb_gallery_images',
			'phpbb_gallery_albums',
			'phpbb_gallery_rates'
		);
	}

	/**
	 * Test rating loader
	 */

	public function test_loader()
	{
		$this->gallery_rating->loader(1);
		$this->assertEquals($this->gallery_rating->image_id, 1);

	}

	/**
	 * Test rating get_image_rating
	 */
	public function test_get_image_rating()
	{
		//empty for now
	}

	/*
	 * Provide data for get get_user_rating
	 */
	public function data_get_user_rating()
	{
		return array(
			'anon' => array(
				1,
				-1
			),
			'uid2'	=> array(
				2,
				10
			),
			'uid3'	=> array(
				3,
				9
			),
			'uid4'	=> array(
				4,
				2
			),
		);
	}
	/**
	 * Test rating get_user_rating
	 *
	 * @dataProvider data_get_user_rating
	 */
	public function test_get_user_rating($uid, $test)
	{
		$this->gallery_rating->loader(1);
		$this->gallery_rating->user_rating = array('4' => 2);
		if ($test == -1)
		{
			$this->assertFalse($this->gallery_rating->get_user_rating($uid));
		}
		else
		{
			$this->assertEquals($this->gallery_rating->get_user_rating($uid), $test);
		}
	}

	/*
	 * Provide data for get get_submit_rating
	 */
	public function data_submit_rating()
	{
		return array(
			'norm'	=> array(
				2, // Loader
				3, // User Id
				3, // Score
				'1', //IP
				3 // Expected
			),
			/*'change_req' => array(
				2, // Loader
				3, // User Id
				4, // Score
				'1', //IP
				-1 // Expected
			),*/
			'anon' => array(
				2, // Loader
				1, // User Id
				4, // Score
				'1', //IP
				-1 // Expected
			),
			'image_in_Db_norm_change' => array(
				1, // Loader
				2, // User Id
				3, // Score
				'1', //IP
				-1 // Expected
			),
			'image_in_Db_anon' => array(
				1, // Loader
				1, // User Id
				3, // Score
				'1', //IP
				-1 // Expected
			),
		);
	}

	/**
	 * Test rating submit_rating
	 *
	 * @dataProvider data_submit_rating
	 */
	public function test_submit_rating($loader, $user_id, $score, $ip, $expected)
	{
		$this->gallery_rating->loader($loader);
		if ($expected == -1)
		{
			$this->assertFalse($this->gallery_rating->submit_rating($user_id, $score, $ip));
		}
		else
		{
			$this->gallery_rating->submit_rating($user_id, $score, $ip);
			$this->assertEquals($this->gallery_rating->user_rating[$user_id], $expected);
		}


	}
}
