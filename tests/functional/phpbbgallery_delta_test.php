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
	/*
	* Set of test related to finctionality of ACP Import
	*/
	public function test_prepare_import()
	{
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/core/import/copy_to_public_no_change.jpg'));
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/core/import/copy_to_public_change_uploader.jpg'));
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/core/import/copy_to_public_change_image_name.jpg'));
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/core/import/copy_to_personal_existing.jpg'));
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/core/import/copy_to_personal_non_existing.jpg'));
	}
	public function test_acp_import_no_change()
	{
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_acpimport');
		
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);
		$album_id = $crawler->filter('option:contains("First test album!")')->attr('value');
		$form = $crawler->selectButton('submit')->form();
		$form['name'] = array('copy_to_public_no_change.jpg');
		$form['album_id'] = $album_id;
		$crawler = self::submit($form);
		
		$this->assertLangContains('IMPORT_SCHEMA_CREATED', $crawler->text());
		
		$crawler = self::$client->followRedirect();
		
		$this->assertContains('uploaded', $crawler->text());
		
		$crawler = self::$client->followRedirect();
		
		$this->assertEquals(0, $album_id = $crawler->filter('option:contains("copy_to_public_no_change.jpg")')->count();
		
		$this->logout();
		$this->logout();
		
	}
}