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
		
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-main_module&mode=overview&sid=' . $this->sid);
		$this->assertContainsLang('ACP_GALLERY_OVERVIEW_EXPLAIN', $crawler->text());
		
		$this->logout();
		$this->logout();
	}
	
	public function test_exif_install()
	{
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/exif', 'exif');
		
		$crawler = self::request('GET', 'adm/index.php?i=--phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		
		$this->assertContainsLang('DISP_EXIF_DATA', $crawler->text());
		
		$this->logout();
		$this->logout();
	}
}