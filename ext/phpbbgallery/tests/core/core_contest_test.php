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


class core_contest_test extends core_base
{
	protected $gallery_contest;

	public function setUp() : void
	{
		parent::setUp();

		$this->gallery_contest = new \phpbbgallery\core\contest(
			$this->db,
			$this->gallery_config,
			'phpbb_gallery_images',
			'phpbb_gallery_contests'
		);
	}

	public function test_get_contest()
	{
		$test = $this->gallery_contest->get_contest(1);
		$this->assertEquals($test['contest_album_id'], 5);
	}
	public function test_get_contest_album_parm()
	{
		$test = $this->gallery_contest->get_contest(6, 'album');
		$this->assertEquals($test['contest_id'], 2);
	}

	/*
	 * Provide data for get is_step
	 */
	public function data_is_step()
	{
		$time = time();
		return array(
			'contest_in_upload' => array(
				array(//album data
					'contest_id' => 1,
					'contest_start'	=> $time - 20,
					'contest_rating'	=> $time + 50,
					'contest_end'	=> $time + 100,
				),
				true,
				false,
				false

			),
			'contest_in_rate' => array(
				array(//album data
					  'contest_id' => 1,
					  'contest_start'	=> $time - 20,
					  'contest_rating'	=> $time - 10,
					  'contest_end'	=> $time + 100,
				),
				true,
				false,
				false
			),
			'contest_in_comment' => array(
				array(//album data
					  'contest_id' => 1,
					  'contest_start'	=> $time - 50,
					  'contest_rating'	=> $time - 20,
					  'contest_end'	=> $time - 10,
				),
				true,
				false,
				false
			),
		);
	}


	/**
	 * Test is_step
	 * @dataProvider data_is_step
	 **/

	public function test_is_step($album_data, $upload, $rating, $comment)
	{
		$this->assertEquals($upload, $this->gallery_contest->is_step('upload', $album_data));
		$this->assertEquals($rating, $this->gallery_contest->is_step('rate', $album_data));
		$this->assertEquals($comment, $this->gallery_contest->is_step('comment', $album_data));
	}
}
