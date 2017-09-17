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
			$this->user,
			$this->config,
			$this->auth,
			'phpbb_gallery_users',
			'/',
			'php'
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
		$this->gallery_user->destroy();
	}


	/**
	* Test update images
	*/
	public function test_update_images()
	{
		$this->gallery_user->set_user_id(2);
		$this->assertEquals($this->gallery_user->get_data('user_images'), 0);
		$this->gallery_user->update_images(5);
		$this->assertEquals($this->gallery_user->get_data('user_images'), 5);
		$this->gallery_user->update_images(5);
		$this->assertEquals($this->gallery_user->get_data('user_images'), 10);
		$this->gallery_user->update_images(-10);
		$this->assertEquals($this->gallery_user->get_data('user_images'), 0);
		$this->gallery_user->destroy();
	}

	/**
	* Test delete user
	* Before deleting them we will have to create few users
	*/
	public function test_delete()
	{
		// First we will create 3 users
		$this->gallery_user->set_user_id(53);
		$this->gallery_user->update_data(array('personal_album_id' => 2));
		$this->gallery_user->set_user_id(54);
		$this->gallery_user->update_data(array('personal_album_id' => 3));
		$this->gallery_user->set_user_id(55);
		$this->gallery_user->update_data(array('personal_album_id' => 4));
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_id > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$this->assertEquals(5, $row['count']);
		$this->gallery_user->delete();
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_id > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$this->assertEquals(4, $row['count']);
		$this->gallery_user->destroy();
	}

	/**
	* Test delete users
	* Test single ID, array of IDs or string of all
	*/
	public function test_delete_users()
	{
		$this->gallery_user->set_user_id(55);
		$this->gallery_user->update_data(array('personal_album_id' => 4));
		$this->gallery_user->delete_users(array(53, 54));
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_id > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$this->assertEquals(3, $row['count']);
		$this->gallery_user->delete_users(52);
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_id > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$this->assertEquals(2, $row['count']);
		$this->gallery_user->delete_users('all');
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_id > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$this->assertEquals(0, $row['count']);
	}

	/**
	* Test update users
	* Test single ID, array of IDs or string of all
	* First we create 5 new users
	*/
	public function test_update_users()
	{
		$this->gallery_user->set_user_id(2);
		$this->gallery_user->update_data(array('personal_album_id' => 1));
		$this->gallery_user->set_user_id(52);
		$this->gallery_user->update_data(array('personal_album_id' => 2));
		$this->gallery_user->set_user_id(53);
		$this->gallery_user->update_data(array('personal_album_id' => 3));
		$this->gallery_user->set_user_id(54);
		$this->gallery_user->update_data(array('personal_album_id' => 4));
		$this->gallery_user->set_user_id(55);
		$this->gallery_user->update_data(array('personal_album_id' => 5));
		$this->gallery_user->destroy();
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_images > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->assertEquals(0, $row['count']);
		$data = array(
			'user_images' => 3,
		);
		$this->gallery_user->update_users(2, $data);
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_images > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->assertEquals(1, $row['count']);

		$this->gallery_user->update_users(array(52,53), $data);
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_images > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->assertEquals(3, $row['count']);

		$this->gallery_user->update_users('all', $data);
		$sql = 'SELECT COUNT(user_id) as count FROM phpbb_gallery_users WHERE user_images > 0';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->assertEquals(5, $row['count']);
	}

	public function data_sql_build_where()
	{
		return array(
			'single' => array(
				2,
				'WHERE user_id = 2'
			),
			'array' => array(
				array(2,3),
				'WHERE user_id IN (2, 3)',
			),
			'all' => array(
				'all',
				'',
			),
		);
	}

	/**
	* Test SQL Build Where
	* @dataProvider data_sql_build_where
	*/
	public function test_sql_build_where($input, $expected)
	{
		if ($input == 'all')
		{
			$this->assertEmpty($this->gallery_user->sql_build_where($input));
		}
		else
		{
			$this->assertContains($expected, $this->gallery_user->sql_build_where($input));
		}
	}

	public function test_get_own_root_album()
	{
		$this->gallery_user->set_user_id(2);
		$this->assertEquals(1, $this->gallery_user->get_own_root_album());
		$this->gallery_user->set_user_id(3);
		$this->assertEquals(0, $this->gallery_user->get_own_root_album());
	}
}