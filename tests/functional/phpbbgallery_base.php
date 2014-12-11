<?php
/**
*
* Gallery Tests
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
namespace phpbbgallery\tests\functional;
/**
* @group functional
*/
class phpbbgallery_base extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('phpbbgallery/core', 'phpbbgallery/exif', 'phpbbgallery/acpimport');
	}
	public function setUp()
	{
		parent::setUp();
	}	
}