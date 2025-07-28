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
* @group functional1
*/
class phpbbgallery_delta_test extends phpbbgallery_base
{
	/*
	* Set of test related to finctionality of ACP Import
	*/
	public function test_prepare_import()
	{
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/import/copy_to_public_no_change.jpg'));
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/import/copy_to_public_change_uploader.jpg'));
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/import/copy_to_public_change_image_name.jpg'));
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/import/copy_to_personal_existing.jpg'));
		$this->assertEquals(1, copy(__DIR__ . '/images/valid.jpg', __DIR__ . '/../../../../files/phpbbgallery/import/copy_to_personal_non_existing.jpg'));
	}
/*	public function test_acp_import_no_change()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_acpimport');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);
		$album_id = $crawler->filter('option:contains("First test album!")')->attr('value');
		$form = $crawler->selectButton('submit')->form();
		$form['images'] = array('copy_to_public_no_change.jpg');
		$form['album_id'] = $album_id;
		$crawler = self::submit($form);

		$this->assertContainsLang('IMPORT_SCHEMA_CREATED', $crawler->text());

		$meta = $crawler->filter('meta[http-equiv="refresh"]')->attr('content');
		$this->assertContains('adm', $meta);

		$url = $this->get_url_from_meta($meta);
		var_dump(substr($url, 17));
		$crawler = self::request('GET', substr($url, 17));

		$this->assertContains('images successful imported', $crawler->text());


		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);

		$this->assertEquals(0, $album_id = $crawler->filter('option:contains("copy_to_public_no_change.jpg")')->count());

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$this->assertContains('copy to public no change', $crawler->text());

		$this->logout();
		$this->logout();

	}
	public function test_acp_import_change_uploader()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_acpimport');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);
		$album_id = $crawler->filter('option:contains("First test album!")')->attr('value');
		$form = $crawler->selectButton('submit')->form();
		$form['images'] = array('copy_to_public_change_uploader.jpg');
		$form['album_id'] = $album_id;
		$form['username'] = 'testuser1';
		$crawler = self::submit($form);

		$this->assertContainsLang('IMPORT_SCHEMA_CREATED', $crawler->text());

		$meta = $crawler->filter('meta[http-equiv="refresh"]')->attr('content');
		$this->assertContains('adm', $meta);

		$url = $this->get_url_from_meta($meta);
		$crawler = self::request('GET', substr($url, 17));

		$this->assertContains('images successful imported', $crawler->text());


		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);

		$this->assertEquals(0, $album_id = $crawler->filter('option:contains("copy_to_public_change_uploader.jpg")')->count());

		$crawler = self::request('GET', 'app.php/gallery/album/1');
		$this->assertContains('copy to public change uploader', $crawler->text());
		$this->assertContains('testuser1', $crawler->filter('div:contains("copy to public change uploader")')->text());

		$this->logout();
		$this->logout();
	}
	public function test_acp_import_change_image_name()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_acpimport');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);
		$album_id = $crawler->filter('option:contains("First test album!")')->attr('value');
		$form = $crawler->selectButton('submit')->form();
		$form['images'] = array('copy_to_public_change_image_name.jpg');
		$form['album_id'] = $album_id;
		$form['image_name'] = 'Test image change';
		$crawler = self::submit($form);

		$this->assertContainsLang('IMPORT_SCHEMA_CREATED', $crawler->text());

		$meta = $crawler->filter('meta[http-equiv="refresh"]')->attr('content');
		$this->assertContains('adm', $meta);

		$url = $this->get_url_from_meta($meta);
		$crawler = self::request('GET', substr($url, 17));

		$this->assertContains('images successful imported', $crawler->text());


		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);

		$this->assertEquals(0, $album_id = $crawler->filter('option:contains("copy_to_public_change_image_name.jpg")')->count());

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$this->assertContains('Test image change', $crawler->text());

		$this->logout();
		$this->logout();

	}
	public function test_acp_import_add_to_user_gallery_existing()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_acpimport');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);
		//$album_id = $crawler->filter('option:contains("First test album!")')->attr('value');
		$form = $crawler->selectButton('submit')->form();
		$form['images'] = array('copy_to_personal_existing.jpg');
		$form['users_pega'] = 1;
		$crawler = self::submit($form);

		$this->assertContainsLang('IMPORT_SCHEMA_CREATED', $crawler->text());

		$meta = $crawler->filter('meta[http-equiv="refresh"]')->attr('content');
		$this->assertContains('adm', $meta);

		$url = $this->get_url_from_meta($meta);
		$crawler = self::request('GET', substr($url, 17));

		$this->assertContains('images successful imported', $crawler->text());


		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);

		$this->assertEquals(0, $album_id = $crawler->filter('option:contains("copy_to_personal_existing.jpg")')->count());

		$crawler = self::request('GET', 'app.php/gallery/users');
		$url = $crawler->filter('div.polaroid')->filter('a:contains("admin")')->attr('href');
		$crawler = self::request('GET', substr($url, 1));
		$this->assertContains('copy to personal existing', $crawler->text());

		$this->logout();
		$this->logout();
	}
	public function test_acp_import_add_to_user_gallery_not_existing()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_acpimport');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);
		//$album_id = $crawler->filter('option:contains("First test album!")')->attr('value');
		$form = $crawler->selectButton('submit')->form();
		$form['images'] = array('copy_to_personal_non_existing.jpg');
		$form['users_pega'] = 1;
		$form['username'] = 'testuser1';
		$crawler = self::submit($form);

		$this->assertContainsLang('IMPORT_SCHEMA_CREATED', $crawler->text());

		$meta = $crawler->filter('meta[http-equiv="refresh"]')->attr('content');
		$this->assertContains('adm', $meta);

		$url = $this->get_url_from_meta($meta);
		$crawler = self::request('GET', substr($url, 17));

		$this->assertContains('images successful imported', $crawler->text());


		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);

		$this->assertEquals(0, $album_id = $crawler->filter('option:contains("copy_to_personal_non_existing.jpg")')->count());

		$crawler = self::request('GET', 'app.php/gallery/users');
		$url = $crawler->filter('div.polaroid')->filter('a:contains("testuser1")')->attr('href');
		$crawler = self::request('GET', substr($url, 1));
		$this->assertContains('copy to personal non existing', $crawler->text());

		$this->logout();
		$this->logout();
	}*/
	public function exif_data()
	{
		return array(
			'upload_yes'	=> array(
				'first',
				1
			),
			'upload_no'	=> array(
				'first',
				0
			),
			/*'import_yes'	=> array(
				'last',
				1
			),
			'import_no'	=> array(
				'last',
				0
			),*/
			'reset'	=> array(
				'first',
				1
			),
		);
	}
	/**
	* @dataProvider exif_data
	*/
	public function test_exif($image, $state)
	{
		$this->login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/exif', 'exif');

		$this->set_option('disp_exifdata', $state);
		if ($image == 'first')
		{
			$crawler = self::request('GET', 'app.php/gallery/image/1');
		}
		else
		{
			$crawler = self::request('GET', 'app.php/gallery/users');
			$url = $crawler->filter('div.polaroid')->filter('a:contains("testuser1")')->attr('href');
			$crawler = self::request('GET', substr($url, 1));
			$url = $crawler->filter('a:contains("copy to personal non existing")')->attr('href');
			$crawler = self::request('GET', substr($url, 1));
		}

		if ($state == 1)
		{
			$this->assertContainsLang('EXIF_DATA', $crawler->text());
			$this->assertContainsLang('EXIF_CAM_MODEL', $crawler->text());
		}
		else
		{
			$this->assertNotContainsLang('EXIF_DATA', $crawler->text());
			$this->assertNotContainsLang('EXIF_CAM_MODEL', $crawler->text());
		}
	}
}