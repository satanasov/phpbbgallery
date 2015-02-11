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
class phpbbgallery_beta_test extends phpbbgallery_base
{
	public function test_thumbnail_link()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('common');
		
		// Test image_page
		$this->config_set('link_thumbnail', 'image_page');
		$crawler = self::request('GET', 'app.php/gallery');
		$object = $crawler->filter('a:contains("Valid")')->parents()->parents();
		$link = $object->filter('img')->parents()->attr('href');
		$this->assertContains('gallery/image/', $link);
		
		// Test image
		$this->config_set('link_thumbnail', 'image');
		$crawler = self::request('GET', 'app.php/gallery');
		$object = $crawler->filter('a:contains("Valid")')->parents()->parents();
		$link = $object->filter('img')->parents()->attr('href');
		$this->assertContains('/source', $link);
		$this->logout();
	}
	
	// Test if disabling core will disable all other extensions
	public function test_togle_core()
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('common');
		$this->add_lang('acp/extensions');
		
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=disable_pre&ext_name=phpbbgallery%2Fcore&sid=' . $this->sid);
		$form = $crawler->selectButton($this->lang('DISABLE'))->form();
		$crawler = self::submit($form);
		
		$this->assertContainsLang('EXTENSION_DISABLE_SUCCESS', $crawler->text());
		
		$exts_array = array('phpbbgallery/core', 'phpbbgallery/exif', 'phpbbgallery/acpimport', 'phpbbgallery/acpcleanup');
		foreach ($exts_array as $ext)
		{
			$this->assertEquals(0, $this->get_state($ext));
		}
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=enable_pre&ext_name=phpbbgallery%2Fcore&sid=' . $this->sid);
		$form = $crawler->selectButton($this->lang('ENABLE'))->form();
		$crawler = self::submit($form);
		foreach ($exts_array as $ext)
		{
			$this->assertEquals(1, $this->get_state($ext));
		}
		$this->logout();
	}
}