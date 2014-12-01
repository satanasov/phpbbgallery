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
class phpbbgallery_core_acp_test extends phpbbgallery_base
{
	public function test_install()
	{
		$this->login();
		$this->admin_login();
		
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-main_module&mode=overview&sid=' . $this->sid);
		$this->assertContains('zazazazazaza', $crawler->text());
	}
}