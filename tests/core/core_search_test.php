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

class core_search_test extends core_base
{
	public function setUp()
	{
		parent::setUp();
		$this->gallery_config = new \phpbbgallery\core\config(
			$this->config
		);
	}
	
	// Dummy test
	public function test_dummy()
	{
		$this->assertEquals(11, 11);
	}
}