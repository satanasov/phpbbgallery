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

class core_user_test extends core_base
{
	public function setUp()
	{
		parent::setUp();
		$this->gallery_user = new \phpbbgallery\core\user(
			$this->db,
			$this->dispatcher,
			'phpbb_gallery_users'
		);
	}

	/**
	* Test data for the test_set_user_id test
	*
	* @return array Test data
	*/
	public function data_set_user_id()
	{
		return array(
			'base'	=> array(
				2, // User_id
				true, // Expected
				1 // Album ID
			),
			'non_existing_user'	=> array(
				3, // User_id
				false, // Expected
				0 // Has album
			),
			'existin_user_with_no_album' => array(
				52,
				true,
				0
			)
		);
	}
	/**
	* Test user id and load user data
	*
	* @dataProvider data_set_user_id
	*/
	public function test_set_user_id($user_id, $expected, $album_id)
	{
		$this->gallery_user->set_user_id($user_id);
		if ($expected)
		{
			$this->assertTrue($this->gallery_user->entry_exists);
			$this->assertEquals($this->gallery_user->get_data('personal_album_id'), $album_id);
			$this->assertTrue($this->gallery_user->is_user($user_id));
			$this->assertFalse($this->gallery_user->is_user(1));
			$this->gallery_user->destroy();
			$this->assertNull($this->gallery_user->entry_exists);
			$this->assertNull($this->gallery_user->user_id);
		}
		else
		{
			$this->assertFalse($this->gallery_user->entry_exists);
			$this->gallery_user->destroy();
		}
	}
	/**
	* Test data for the test_udpate_data test
	*
	* @return array Test data
	*/
	public function data_update_data()
	{
		return array(
			'base'	=> array(
				2, // User_id
				2 // Album ID
			),
			'non_existing_user'	=> array(
				3, // User_id
				1 // Has album
			),
		);
	}
	/**
	* Test Update Data
	* @dataProvider data_update_data
	*/
	public function test_udpate_data($user_id, $new_var)
	{
		$this->gallery_user->set_user_id($user_id, false);
		$this->assertTrue($this->gallery_user->update_data(array('personal_album_id' => $new_var)));
		$this->assertEquals($new_var, $this->gallery_user->get_data('personal_album_id'));
		$this->assertTrue($this->gallery_user->entry_exists);
	}
}