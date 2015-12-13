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
* @group core1
*/
require_once dirname(__FILE__) . '/../../../../includes/functions.php';

class core_album_test extends core_base
{
	public function setUp()
	{
		parent::setUp();
		$this->album = new \phpbbgallery\core\album\album(
			$this->db,
			$this->user,
			'phpbb_gallery_albums',
			'phpbb_gallery_watch',
			'phpbb_gallery_contests'
		);
	}

	/**
	* Test get_status_locked
	*/
	public function test_status_status_locked()
	{
		$this->assertEquals($this->album->get_status_locked(), 1);
	}

	/**
	* Test get_public
	*/
	public function test_get_public()
	{
		$this->assertEquals($this->album->get_public(), 0);
	}

	/**
	* Test get_type_upload
	*/
	public function test_get_type_upload()
	{
		$this->assertEquals($this->album->get_type_upload(), 1);
	}

	/**
	* Test get_info
	* Here we test only exception.
	* Normal get info is tested in core_search_test where it is called!
	
	public function test_get_info_fail()
	{
		try
		{
			$this->album->get_info(99);
			$this->fail('This should trow \phpbb\exception\http_exception');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals(404, $exception->getStatusCode());
			$this->assertEquals('ALBUM_NOT_EXIST', $exception->getMessage());
		}
	}
	*/
	/**
	* Test check_user
	* Here we check valid state
	*/
	public function test_check_user_valid()
	{
		$this->user->data['user_id'] = 2;
		$this->assertTrue($this->album->check_user(4));
	}

	/**
	* Test check_user
	* Here we test only exception.
	*
	*/
	public function test_check_user_fail_case_wrong_user()
	{
		$this->user->data['user_id'] = 3;
		try
		{
			$this->assertTrue($this->album->check_user(4));
			$this->fail('This should trow \phpbb\exception\http_exception');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals(403, $exception->getStatusCode());
			$this->assertEquals('NO_ALBUM_STEALING', $exception->getMessage());
		}
	}
	/**
	* Test check_user
	* Here we test only exception.
	*
	*/
	public function test_check_user_fail_case_wrong_passed_user()
	{
		$this->user->data['user_id'] = 2;
		try
		{
			$this->assertTrue($this->album->check_user(4, 3));
			$this->fail('This should trow \phpbb\exception\http_exception');
		}
		catch (\phpbb\exception\http_exception $exception)
		{
			$this->assertEquals(403, $exception->getStatusCode());
			$this->assertEquals('NO_ALBUM_STEALING', $exception->getMessage());
		}
	}	
}