<?php
/**
 *  @package phpBB Gallery
 *  @version 3.2.1.x
 *  @copyright (c) 2018 Stanislav Atanasov s.atanasov@anavaro.com http://www.anavaro.com
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace phpbbgallery\tests\core;

/**
 * @group core_dev
 */

require_once dirname(__FILE__) . '/../../../../includes/functions.php';

class core_notification_test extends core_base
{
	public function setUp()
	{
		parent::setUp();

		$this->notification = new \phpbbgallery\core\notification(
			$this->db,
			$this->user,
			'phpbb_gallery_watch'
		);
	}

	/*
	 * Provide data for get user_info
	 */
	public function data_add_vew()
	{
		return array(
			'base' => array(
				array((int) 1, (int) 2, (int) 3, (int) 4), // albums as array
				2, // user_id forced
				4 // expected
			),
			'int' => array(
				(int) 1, // albums as array
				2, // user_id forced
				1 // expected
			),
			'str' => array(
				'1', // albums as array
				2, // user_id forced
				1 // expected
			),
			'dup' => array(
				(int) 6, // albums as array
				6, // user_id forced
				2 // expected
			)
		);
	}

	/**
	 * Test add_albums
	 * @dataProvider data_add_vew
	 **/
	public function test_add($albums, $user_id, $expected)
	{
		$this->notification->add($albums, $user_id);
		$this->db->sql_query('SELECT COUNT(watch_id) as eq1 FROM phpbb_gallery_watch WHERE user_id = ' . $user_id);
		$answer = $this->db->sql_fetchfield('eq1');
		$this->assertEquals($answer, $expected);
	}

	/**
	 * Test add_albums
	 * @dataProvider data_add_vew
	 **/
	public function test_add_albums($albums, $user_id, $expected)
	{
		$this->notification->add_albums($albums, $user_id);
		$this->db->sql_query('SELECT COUNT(watch_id) as eq1 FROM phpbb_gallery_watch WHERE user_id = ' . $user_id);
		$answer = $this->db->sql_fetchfield('eq1');
		$this->assertEquals($answer, $expected);
	}

	public function test_remove()
	{
		$this->notification->remove(6, 6);
		$this->notification->remove_albums(6, 6);
		$this->db->sql_query('SELECT COUNT(watch_id) as eq1 FROM phpbb_gallery_watch WHERE user_id = 6');
		$answer = $this->db->sql_fetchfield('eq1');
		$this->assertEquals($answer, 0);
	}

	public function test_delete()
	{
		$this->notification->delete_images(array(6));
		$this->notification->delete_albums(6);
		$this->db->sql_query('SELECT COUNT(watch_id) as eq1 FROM phpbb_gallery_watch WHERE user_id = 6');
		$answer = $this->db->sql_fetchfield('eq1');
		$this->assertEquals($answer, 0);
	}
}