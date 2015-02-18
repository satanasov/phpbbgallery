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
	public function image_on_image_page_data()
	{
		return array(
			'image'	=> array(
				'image',
				true,
				'app.php/gallery/image/1/source'
			),
			'next'	=> array(
				'next',
				true,
				'app.php/gallery/image/2'
			),
			'none'	=> array(
				'none',
				false,
				false
			),
		);
	}
	/**
	* @dataProvider image_on_image_page_data
	*/
	public function test_image_on_image_page($option, $has_link, $search)
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang('common');

		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[link_imagepage]'	=> $option,
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());

		// Test image
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		if ($has_link)
		{
			$link = $crawler->filter('div#image')->filter('a')->attr('href');
			$this->assertContains($search, $link);
		}
		else
		{
			$this->assertEquals(0, $crawler->filter('div#image')->filter('a')->count());
			$this->assertEquals(1, $crawler->filter('div#image')->filter('img')->count());
		}		
		$this->logout();
	}
	public function thumbnail_link_data()
	{
		return array(
			'image'	=> array(
				'image',
				true,
				'app.php/gallery/image/2/source'
			),
			'image_page'	=> array(
				'image_page',
				true,
				'app.php/gallery/image/2'
			),
			'none'	=> array(
				'none',
				false,
				false
			),
		);
	}
	/**
	* @dataProvider thumbnail_link_data
	*/
	public function test_thumbnail_link($option, $has_link, $search)
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang('common');
		
		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[link_thumbnail]'	=> $option,
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/album/1');
		$object = $crawler->filter('div.polaroid')->eq(2)->filter('div#thumbnail');
		if ($has_link)
		{
			$this->assertContains($search, $object->filter('a')->attr('href'));
		}
		else
		{
			$this->assertEquals(0, $object->filter('a')->count());
			$this->assertEquals(1, $object->filter('img')->count());
		}
		
		$this->logout();
	}
	/**
	* @dataProvider thumbnail_link_data
	*/
	public function test_image_name_link($option, $has_link, $search)
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang('common');
		
		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[link_image_name]'	=> $option,
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/album/1');
		$object = $crawler->filter('div.polaroid')->eq(2)->filter('p')->eq(0);
		if ($has_link)
		{
			$this->assertContains($search, $object->filter('a')->attr('href'));
		}
		else
		{
			$this->assertEquals(0, $object->filter('a')->count());
		}
		
		$this->logout();
	}
}