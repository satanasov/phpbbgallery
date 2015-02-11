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
	public function test_toglle_core()
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
	// Create album for testing
	public function test_admin_create_album()
	{
		$this->login();
		$this->admin_login();
		
		// Let us create a user we will use for tests
		$this->create_user('testuser1');
		$this->add_user_group('REGISTERED', array('testuser1'));
		// Let me get admin out of registered
		$this->remove_user_group('REGISTERED', array('admin'));
		
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);
		
		// Step 1
		$form = $crawler->selectButton($this->lang('CREATE_ALBUM'))->form();
		$form['album_name'] = 'First test album!';
		$crawler = self::submit($form);
		
		// Step 2 - we should have reached a form for creating album_name
		$this->assertContainsLang('ALBUM_EDIT_EXPLAIN', $crawler->text());
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$crawler = self::submit($form);
		
		// Step 3 - Album should be created and we should have option to add permissions
		$this->assertContainsLang('ALBUM_CREATED', $crawler->text());
		
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);
		$this->assertContains('First test album!', $crawler->text());
		
		$crawler = self::request('GET', 'app.php/gallery');
		$this->assertContains('First test album!', $crawler->text());
		
		$this->logout();
		$this->logout();
	}
}