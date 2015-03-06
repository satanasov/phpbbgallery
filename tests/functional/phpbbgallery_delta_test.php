<?php
/**
* 
* Gallery Control test
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
namespace phpbbgallery\tests\functional;
/**
* @group functional
*/
class phpbbgallery_delta_test extends phpbbgallery_base
{
	public function test_prepare_import()
	{
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../files/core/import/1.jpg'));
	}
}