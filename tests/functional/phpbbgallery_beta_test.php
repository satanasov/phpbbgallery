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
	// TESTS DATA PROVIDERS
	public function pagination_data()
	{
		return array(
			'pages'	=> array(
				1
			),
			'reset'	=> array(
				15
			),
		);
	}
	
	public function yes_no_data()
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
	// START BASIC GALLERY SETTINGS TESTS
	/**
	* @dataProvider pagination_data
	*/
	public function test_items_per_page_paginate($option)
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
			'config[items_per_page]'	=> $option,
		));

		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		// Test
		$crawler = self::request('GET', 'app.php/gallery/album/1');
		
		if ($option == 1)
		{
			$this->assertEquals(1, $crawler->filter('div.polaroid')->count());
		}
		else
		{
			$this->assertEquals(3, $crawler->filter('div.polaroid')->count());
		}
		
		$this->logout();
		$this->logout();
	}
	
	/**
	* @dataProvider yes_no_data
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
	* @dataProvider yes_no_data
	*/
	public function test_comment_user_control_option($option)
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_ucp');
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
		$crawler = self::request('GET', 'ucp.php?i=-phpbbgallery-core-ucp-settings_module&mode=manage&sid' . $this->sid);
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
	public function test_comment_to_many_symbols_user()
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_ucp');
		$this->add_lang('common');
		
		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[comment_length]'	=> 1,
		));

		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		$this->logout();
		$this->logout();
		
		// Test
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		
		$form = $crawler->selectButton('submit')->form();
		$form['message'] = 'Test comment that should be seen';
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('COMMENT_TOO_LONG', $crawler->text());
		
		$this->logout();
		
		// Reset
		$this->login();
		$this->admin_login();
		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[comment_length]'	=> 2000,
		));

		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		$this->logout();
		$this->logout();
		
	}
	/**
	* @dataProvider yes_no_data
	*/
	public function test_allow_rates($option)
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_ucp');
		$this->add_lang('common');
		
		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[allow_rates]'	=> $option,
		));

		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		// test
		$this->logout();
		$this->logout();
		
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		if ($option == 1)
		{
			$this->assertContains($this->lang('RATING'), $crawler->text());
		}
		else
		{
			$this->assertNotContains($this->lang('RATING'), $crawler->text());
		}
		$this->logout();
	}
	public function test_max_rating()
	{
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		
		$this->assertEquals(11, $crawler->filter('select:contains("'.$this->lang('DONT_RATE_IMAGE').'")')->filter('option')->count());
		$this->logout();
		
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_ucp');
		$this->add_lang('common');
		
		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[max_rating]'	=> 20,
		));

		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		// test
		$this->logout();
		$this->logout();
		
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		
		$this->assertEquals(21, $crawler->filter('select:contains("'.$this->lang('DONT_RATE_IMAGE').'")')->filter('option')->count());
		$this->logout();
		
		$this->login();
		$this->admin_login();
		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[max_rating]'	=> 10,
		));

		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		// test
		$this->logout();
		$this->logout();
	}
	public function test_rate()
	{
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		
		$form = $crawler->filter('select:contains("'.$this->lang('DONT_RATE_IMAGE').'")')->parents()->parents()->parents()->form();
		$form['rating'] = 5;
		$crawler = self::submit($form);
		
		$this->assertContainsLang('RATING_SUCCESSFUL', $crawler->text());
		
		$meta = $crawler->filter('meta[http-equiv="refresh"]')->attr('content');
		$url = $this->get_url_from_meta($meta);
		$crawler = self::request('GET', substr($url, 1));
		
		$this->assertContains('rating, your rating:', $crawler->text());
		$this->logout();
	}
    // END BASIC GALLERY SETTINGS TESTS

	// START ALBUM SETTINGS TESTS
	public function image_polaroid_info_data()
	{
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('common');
		return array(
			'none'	=> array(
				array(0),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> false,
				),
			),
			'only_album'	=> array(
				array(1),
				array(
					$this->lang('ALBUM')	=> true,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> false,
				),
			),
			'only_comments'	=> array(
				array(2),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> false,
				),
			),
			'only_name'	=> array(
				array(4),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> false,
				),
			),
			'only_date'	=> array(
				array(8),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> false,
				),
			),
			'only_views'	=> array(
				array(16),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> false,
				),
			),
			'only_user'	=> array(
				array(32),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> false,
				),
			),
			'only_rating'	=> array(
				array(64),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> false,
				),
			),
			'only_ip'	=> array(
				array(128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> true,
				),
			),
			'album_and_ip'	=> array(
				array(1, 128),
				array(
					$this->lang('ALBUM')	=> true,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> true,
				),
			),
			'comments_and_ip'	=> array(
				array(2, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> true,
				),
			),
			'name_and_ip'	=> array(
				array(4, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> true,
				),
			),
			'date_and_iip'	=> array(
				array(8, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> true,
				),
			),
			'image_views_and_ip'	=> array(
				array(16, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> true,
				),
			),
			'user_and_ip'	=> array(
				array(32, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> false,
					$this->lang('IP')	=> true,
				),
			),
			'rating_and_ip'	=> array(
				array(64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'album_rating_and_ip'	=> array(
				array(1, 64, 128),
				array(
					$this->lang('ALBUM')	=> true,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'comments_rating_and_ip'	=> array(
				array(2, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'name_rating_and_ip'	=> array(
				array(4, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'date_rating_and_ip'	=> array(
				array(8, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'image_views_rating_and_ip'	=> array(
				array(16, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> false,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'user_rating_and_ip'	=> array(
				array(32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'album_user_rating_and_ip'	=> array(
				array(1, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> true,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'comments_user_rating_and_ip'	=> array(
				array(2, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'name_user_rating_and_ip'	=> array(
				array(4, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'date_user_rating_and_ip'	=> array(
				array(8, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> false,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'image_vews_user_rating_and_ip'	=> array(
				array(16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'album_image_vews_user_rating_and_ip'	=> array(
				array(1, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> true,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'comments_image_vews_user_rating_and_ip'	=> array(
				array(2, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'name_image_vews_user_rating_and_ip'	=> array(
				array(4, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> false,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'date_image_vews_user_rating_and_ip'	=> array(
				array(8, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'album_date_image_vews_user_rating_and_ip'	=> array(
				array(1, 8, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> true,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'comments_date_image_vews_user_rating_and_ip'	=> array(
				array(2, 8, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> false,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'name_date_image_vews_user_rating_and_ip'	=> array(
				array(4, 8, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'album_name_date_image_vews_user_rating_and_ip'	=> array(
				array(1, 4, 8, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> true,
					$this->lang('COMMENT')	=> false,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'comments_name_date_image_vews_user_rating_and_ip'	=> array(
				array(2, 4, 8, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'album_comments_name_date_image_vews_user_rating_and_ip'	=> array(
				array(1, 2, 4, 8, 16, 32, 64, 128),
				array(
					$this->lang('ALBUM')	=> true,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
			'reset'	=> array(
				array(2,4,8,16,32,64,128),
				array(
					$this->lang('ALBUM')	=> false,
					$this->lang('COMMENT')	=> true,
					'Valid'	=> true,
					$this->lang('UPLOADED_ON_DATE')	=> true,
					$this->lang('IMAGE_VIEWS')	=> true,
					$this->lang('UPLOADED_BY_USER')	=> true,
					$this->lang('RATING')	=> true,
					$this->lang('IP')	=> true,
				),
			),
		);
	}
	/**
	* @dataProvider image_polaroid_info_data
	*/
	public function test_album_display($options, $tests)
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
			'album_display'	=> $options,
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());

		// Test
		$crawler = self::request('GET', 'app.php/gallery/album/1');
		$object = $crawler->filter('div.polaroid')->eq(2);
		
		foreach ($tests as $test => $state)
		{
			if ($state)
			{
				$this->assertContains($test, $object->text());
			}
			else
			{
				$this->assertNotContains($test, $object->text());
			}
		}

		$this->logout();
		$this->logout();
	}

	public function sort_key_data()
	{
		return array(
			'time_desc'	=> array(
				't',
				'd',
				'Image in sublabum to move',
				'Valid but needs approve',
				'Valid',
			),
			'time_asc'	=> array(
				't',
				'a',
				'Valid',
				'Valid but needs approve',
				'Image in sublabum to move',
			),
			'name_desc'	=> array(
				'n',
				'd',
				'Valid but needs approve',
				'Valid',
				'Image in sublabum to move',
			),
			'name_asc'	=> array(
				'n',
				'a',
				'Image in sublabum to move',
				'Valid',
				'Valid but needs approve',
			),
			'view_count_desc'	=> array(
				'vc',
				'd',
				'Valid',
				'Image in sublabum to move',
				'Valid but needs approve',
			),
			'view_count_asc'	=> array(
				'vc',
				'a',
				'Valid but needs approve',
				'Image in sublabum to move',
				'Valid',
			),
			'username_desc'	=> array(
				'u',
				'd',
				'Valid but needs approve',
				'Image in sublabum to move',
				'Valid',
			),
			'username_asc'	=> array(
				'u',
				'a',
				'Valid',
				'Image in sublabum to move',
				'Valid but needs approve',
			),
			'rating_asc'	=> array(
				'ra',
				'a',
				'Image in sublabum to move',
				'Valid but needs approve',
				'Valid',
			),
			'rating_desc'	=> array(
				'ra',
				'd',
				'Valid',
				'Valid but needs approve',
				'Image in sublabum to move',
			),
			'rating_count_asc'	=> array(
				'r',
				'a',
				'Image in sublabum to move',
				'Valid but needs approve',
				'Valid',
			),
			'rating_count_desc'	=> array(
				'r',
				'd',
				'Valid',
				'Valid but needs approve',
				'Image in sublabum to move',
			),
			'comment_asc'	=> array(
				'c',
				'a',
				'Valid but needs approve',
				'Image in sublabum to move',
				'Valid',
			),
			'comment_desc'	=> array(
				'c',
				'd',
				'Valid',
				'Image in sublabum to move',
				'Valid but needs approve',
			),
			'last_comment_asc'	=> array(
				'lc',
				'a',
				'Valid but needs approve',
				'Image in sublabum to move',
				'Valid',
			),
			'last_comment_desc'	=> array(
				'lc',
				'd',
				'Valid',
				'Image in sublabum to move',
				'Valid but needs approve',
			),
			'reset'	=> array(
				't',
				'd',
				'Image in sublabum to move',
				'Valid but needs approve',
				'Valid',
			),
		);
	}
	/**
	* @dataProvider sort_key_data
	*/
	public function test_default_sort_key($sort_key, $sort_dir, $first, $second, $third)
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
			'config[default_sort_key]'	=> $sort_key,
			'config[default_sort_dir]'	=> $sort_dir,
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());

		// Test
		$crawler = self::request('GET', 'app.php/gallery/album/1');
		$this->assertContains($first, $crawler->filter('div.polaroid')->eq(0)->text());
		$this->assertContains($second, $crawler->filter('div.polaroid')->eq(1)->text());
		$this->assertContains($third, $crawler->filter('div.polaroid')->eq(2)->text());
		
		$url = $crawler->filter('div.polaroid')->eq(0)->filter('p')->filter('a:contains')->attr('href');
		$crawler = self::request('GET', $url);
		
		$this->assertContains($second, $crawler->filter('div.image_next_image')->text());
		
		$this->logout();
		$this->logout();
		
	}
	public function test_album_images()
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
			'config[album_images]'	=> 3,
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());

		// Test
		$crawler = self::request('GET', 'app.php/gallery/album/1');
		$upload_url = substr($crawler->filter('a:contains("' . $this->lang('UPLOAD_IMAGE') . '")')->attr('href'), 1);	
		
		$crawler = self::request('GET', $upload_url);

		$this->assertNotContains('This album has reached the quota of images. You cannot upload images anymore.', $crawler->text());
		
		// Change option
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'config[album_images]'	=> 2500,
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('GALLERY_CONFIG_UPDATED', $crawler->text());
		
		$this->logout();
		$this->logout();

	}
	// END ALBUM SETTINGS TESTS
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