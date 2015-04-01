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
	public function allow_comment_data()
	{
		return array(
			'yes'	=> array(
				1
			),
			'no'	=> array(
				0
			),
			'reset'	=> array(
				1
			),
		);
	}
	/**
	* @dataProvider allow_comment_data
	*/
	public function test_allow_comment_option($option)
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
			'config[allow_comments]'	=> $option,
		));

		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		// Test
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		if ($option == 1)
		{
			$this->assertContains($this->lang('POST_COMMENT'), $crawler->text());
		}
		else
		{
			$this->assertNotContains($this->lang('POST_COMMENT'), $crawler->text());
		}
		
		$this->logout();
		$this->logout();
	}
	/**
	* @dataProvider allow_comment_data
	*/
	public function test_comment_user_control_option($option)
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
			'config[comment_user_control]'	=> $option,
		));

		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		// Test
		$crawler = slef::request('GET', 'ucp.php?i=-phpbbgallery-core-ucp-settings_module&mode=manage&sid' . $this->sid);
		if ($option == 1)
		{
			$this->assertContains($this->lang('USER_ALLOW_COMMENTS'), $crawler->text());
		}
		else
		{
			$this->assertNotContains($this->lang('USER_ALLOW_COMMENTS'), $crawler->text());
		}
		$this->logout();
		$this->logout();
	}
	public function test_anon_comment()
	{
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		$this->assertContains($this->lang('CONFIRM_CODE'), $crawler->filter('html')->text());
		
		$crawler = self::request('GET', 'app.php/gallery/comment/1/add/0');
	}
	public function test_comment_user()
	{
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		
		$form = $crawler->selectButton('submit')->form();
		$form['message'] = 'Test comment that should be seen';
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('COMMENT_STORED', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/image/1');
		$this->assertContains('Test comment that should be seen', $crawler->text());
		
		$this->logout();
	}
	public function test_quote_comment()
	{
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');

		$url = $crawler->filter('a:contains("Quote comment")')->attr('href');

		$crawler = self::request('GET', substr($url, 1));
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();

		$crawler = self::submit($form);
		
		$this->assertContainsLang('COMMENT_STORED', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/image/1');

		$this->assertEquals(2, $crawler->filter('div.content:contains("Test comment that should be seen")')->count());
		$this->assertEquals(1, $crawler->filter('div.content:contains("testuser1 wrote:")')->count());
		$this->logout();
	}
	public function test_no_comment()
	{
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		$this->assertEquals(0, $crawler->filter('a:contains("Edit comment")')->count());
		$this->assertEquals(0, $crawler->filter('a:contains("Delete comment")')->count());
		
		$crawler = self::request('GET', 'app.php/gallery/comment/1/edit/1');
		$this->assertContainsLang('USERNAME', $crawler->filter('html')->text());
		
		$crawler = self::request('GET', 'app.php/gallery/comment/1/delete/1');
		$this->assertContainsLang('USERNAME', $crawler->filter('html')->text());
	}
	public function test_edit_comment()
	{
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');

		$url = $crawler->filter('a:contains("Edit comment")')->eq(1)->attr('href');

		$crawler = self::request('GET', substr($url, 1));
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['message'] = 'Test comment that should be edited';
		$crawler = self::submit($form);
		
		$this->assertContainsLang('COMMENT_STORED', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/image/1');

		$this->assertEquals(1, $crawler->filter('div.content:contains("Test comment that should be seen")')->count());
		$this->assertEquals(0, $crawler->filter('div.content:contains("testuser1 wrote:")')->count());
		$this->assertEquals(1, $crawler->filter('div.content:contains("Test comment that should be edited")')->count());
		$this->logout();
	}
	public function test_delete_comment()
	{
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');

		$url = $crawler->filter('a:contains("Delete comment")')->eq(1)->attr('href');

		$crawler = self::request('GET', substr($url, 1));
		
		$form = $crawler->selectButton('confirm')->form();
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('DELETED_COMMENT', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/image/1');

		$this->assertEquals(1, $crawler->filter('div.content:contains("Test comment that should be seen")')->count());
		$this->assertEquals(0, $crawler->filter('div.content:contains("testuser1 wrote:")')->count());
		$this->assertEquals(0, $crawler->filter('div.content:contains("Test comment that should be edited")')->count());
		$this->logout();
	}
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
				'app.php/gallery/image/4'
			),
			'none'	=> array(
				'none',
				false,
				false
			),
			'reset'	=> array(
				'next',
				true,
				'app.php/gallery/image/4'
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
				'app.php/gallery/image/1/source'
			),
			'image_page'	=> array(
				'image_page',
				true,
				'app.php/gallery/image/1'
			),
			'none'	=> array(
				'none',
				false,
				false
			),
			'reset'	=> array(
				'image_page',
				true,
				'app.php/gallery/image/1'
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