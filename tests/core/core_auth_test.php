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

class core_auth_test extends core_base
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
	}

	public function test_get_own_album()
	{
		$this->assertEquals(-2, $this->gallery_auth->get_own_album());
	}
	/**
	* Test data for the get_user_zebra test
	*
	* @return array Test data
	*/
	public function data_get_user_zebra()
	{
		return array(
			'admin'	=> array(
				2,
				array(
					'friend' => array(
						52
					),
					'foe'	=> array(
						53
					),
					'bff' => array(),
				),
			),
			'user1'	=> array(
				52,
				array(
					'friend' => array(
						2
					),
					'foe'	=> array(),
					'bff' => array(),
				),
			),
			'user2'	=> array(
				53,
				array(
					'friend' => array(),
					'foe'	=> array(),
					'bff' => array(),
				),
			),
		);
	}
	/**
	* get_user_zebra
	*
	* @dataProvider data_get_user_zebra
	*/
	public function test_get_user_zebra($user, $expected)
	{
		$zebra = $this->gallery_auth->get_user_zebra($user);
		$this->assertEquals($zebra, $expected);
	}

	/**
	* Test data for the test_acl_check test
	*
	* @return array Test data
	*/
	public function data_acl_check()
	{
		return array(
			'admin'	=> array(
				2, // User ID
				5, // User Group
				1, // album
				'i_view', // ACL to check
				true // Expected
			),
			'user_false'	=> array(
				52,
				2,
				1,
				'm_comments',
				false
			),
			'user_true' => array(
				52,
				2,
				1,
				'i_view',
				true
			),
			'admin_no_rights'	=> array(
				2, // User ID
				5, // User Group
				2, // album
				'i_view', // ACL to check
				true // Expected
			),
			'user_no_rights'	=> array(
				52,
				2,
				2,
				'm_comments',
				false
			),
			'user_false_no_rights' => array(
				52,
				2,
				2,
				'i_view',
				false
			),
			'admin2'	=> array(
				2, // User ID
				5, // User Group
				3, // album
				'i_view', // ACL to check
				false // Expected
			),
		);
	}
	/**
	* acl_check
	*
	* @dataProvider data_acl_check
	*/
	public function test_acl_check($user_id, $group_id, $album_id, $permission, $expected)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group_id'] = $group_id;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if ($expected)
		{
			$this->assertTrue($this->gallery_auth->acl_check($permission, $album_id, 0));
		}
		else
		{
			$this->assertFalse($this->gallery_auth->acl_check($permission, $album_id, 0));
		}
	}
	/**
	* Test data for the set_user_permissions test
	*
	* @return array Test data
	*/
	public function data_set_user_permissions()
	{
		return array(
			array(
				2, // User ID
				5, // Group ID
				//'0:0:0::-2:-3:2:3',
				'0:0:0::-2:-3:3',
				'18219007:0:0::1'
			),
			array(
				52, // User ID
				2, // Group ID
				'0:0:0::-2:-3:2:3',
				'1312767:0:0::1'
			),
			array(
				53, // User ID
				1, // Group ID
				null,
				null
			),
		);
	}
	/**
	* set_user_permissions
	*
	* @dataProvider data_set_user_permissions
	*/
	public function test_set_user_permissions($user_id, $group_id, $expected1, $expected2)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group_id'] = $group_id;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$sql = 'SELECT user_permissions FROM phpbb_gallery_users WHERE user_id = ' . $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		if ($expected1 !== null)
		{
			$this->assertContains($expected1, $row['user_permissions']);
			$this->assertContains($expected2, $row['user_permissions']);
		}
	}

	/**
	* Test data for the acl_check_global test
	*
	* @return array Test data
	*/
	public function acl_check_global_data()
	{
		return array(
			array(
				2, // User ID
				5, // User Group
				'i_view', // permission
				true // result
			),
			array(
				52, // User ID
				2, // User Group
				'i_view', // permission
				true // result
			),
			array(
				53, // User ID
				1, // User Group
				'i_view', // permission
				false // result
			),
			array(
				52, // User ID
				2, // User Group
				'm_comments', // permission
				false // result
			),
			array(
				2, // User ID
				5, // User Group
				'm_comments', // permission
				true // result
			),
		);
	}
	/**
	* set_user_permissions
	*
	* @dataProvider acl_check_global_data
	*/
	public function test_acl_check_global($user_id, $group_id, $acl, $expected)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group_id'] = $group_id;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		if ($expected)
		{
			$this->assertTrue($this->gallery_auth->acl_check_global($acl));
		}
		else
		{
			$this->assertFalse($this->gallery_auth->acl_check_global($acl));
		}
	}

	/**
	* Test data for the acl_album_ids test
	*
	* @return array Test data
	*/
	public function acl_album_ids_data()
	{
		return array(
			array(
				2, // User ID
				5, // Group ID
				'i_view', // ACL
				'array', // Return type
				false, // display_in_rrc
				array( 1, 2) // Expected
			),
			array(
				2, // User ID
				5, // Group ID
				'i_view', // ACL
				'string', // Return type
				false, // display_in_rrc
				'1, 2' // Expected
			),
			array(
				2, // User ID
				5, // Group ID
				'i_view', // ACL
				'array', // Return type
				true, // display_in_rrc
				array(1) // Expected
			),
			array(
				2, // User ID
				5, // Group ID
				'i_view', // ACL
				'string', // Return type
				true, // display_in_rrc
				'1' // Expected
			),
			array(
				52, // User ID
				2, // Group ID
				'i_view', // ACL
				'array', // Return type
				false, // display_in_rrc
				array(1) // Expected
			),
			array(
				52, // User ID
				2, // Group ID
				'i_view', // ACL
				'string', // Return type
				false, // display_in_rrc
				'1' // Expected
			),
			array(
				52, // User ID
				2, // Group ID
				'm_comments', // ACL
				'array', // Return type
				false, // display_in_rrc
				array() // Expected
			),
			array(
				53, // User ID
				1, // Group ID
				'i_view', // ACL
				'string', // Return type
				false, // display_in_rrc
				'' // Expected
			),
		);
	}
	/**
	* acl_album_ids
	*
	* @dataProvider acl_album_ids_data
	*/
	public function test_acl_album_ids($user_id, $group_id, $acl, $return_type, $rrc, $expected)
	{
		$this->user->data['user_id'] = $user_id;
		$this->user->data['group_id'] = $group_id;
		$this->gallery_auth->load_user_premissions($this->user->data['user_id']);
		$actual = $this->gallery_auth->acl_album_ids($acl, $return_type, $rrc);
		if (is_array($expected))
		{
			$this->assertEmpty(array_merge(array_diff($expected, $actual), array_diff($actual, $expected)));
		}
		else
		{
			$this->assertEquals($expected, $this->gallery_auth->acl_album_ids($acl, $return_type, $rrc));
		}
	}

	/**
	* Test data for the acl_user_ids test
	*
	* @return array Test data
	*/
	public function acl_users_ids_data()
	{
		return array(
			array(
				'i_view', // ACL
				1, // album
				array(2, 52) // Expected
			),
			array(
				'm_comments', // ACL
				1, // album
				array(2) // Expected
			),
			array(
				'i_view', // ACL
				2, // album
				array(2) // Expected
			),
		);
	}
	/**
	* acl_user_ids'
	*
	* @dataProvider acl_users_ids_data
	*/
	public function test_acl_users_ids($acl, $album_id, $expected)
	{
		$actual = $this->gallery_auth->acl_users_ids($acl, $album_id);
		$this->assertEmpty(array_merge(array_diff($expected, $actual), array_diff($actual, $expected)));
	}
	/**
	* List of functions to be tested:
	* get_own_album -> test_get_own_album
	* load_user_premissions -> test_acl_check
	* get_user_zebra -> test_get_user_zebra
	* get_zebra_state
	* get_usergroups-> test_acl_check
	* set_user_permissions -> test_set_user_permissions
	* acl_check -> test_acl_check
	* acl_check_global -> test_acl_check_global
	* acl_album_ids - > test_acl_album_ids (TO DO - expand test to personal)
	* acl_users_ids -> test_acl_users_ids (TO DO - expand test to personal)
	* get_exclude_zebra
	*/
}