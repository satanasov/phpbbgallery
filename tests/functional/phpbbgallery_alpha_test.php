<?php
/**
*
* Gallery Control test
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Here we are going to test ACP
*
*/
namespace phpbbgallery\tests\functional;
/**
* @group functional
*/
class phpbbgallery_alpha_test extends phpbbgallery_base
{
	public function install_data()
	{
		return array(
			'core_verview'	=> array(
				'phpbbgallery/core',
				'gallery_acp',
				'adm/index.php?i=-phpbbgallery-core-acp-main_module&mode=overview',
				'ACP_GALLERY_OVERVIEW_EXPLAIN'
			),
			'core_config'	=> array(
				'phpbbgallery/core',
				'gallery_acp',
				'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main',
				'GALLERY_CONFIG'
			),
			'core_albums'	=> array(
				'phpbbgallery/core',
				'gallery_acp',
				'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage',
				'ALBUM_ADMIN'
			),
			'core_perms'	=> array(
				'phpbbgallery/core',
				'gallery_acp',
				'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage',
				'PERMISSIONS_EXPLAIN'
			),
			'core_copy_perms'	=> array(
				'phpbbgallery/core',
				'gallery_acp',
				'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=copy',
				'PERMISSIONS_COPY'
			),
			'core_log'	=> array(
				'phpbbgallery/core',
				'info_acp_gallery_logs',
				'adm/index.php?i=-phpbbgallery-core-acp-gallery_logs_module&mode=main',
				'LOG_GALLERY_SHOW_LOGS'
			),
			// This is core, now extensions
			'exif'	=> array(
				'phpbbgallery/exif',
				'exif',
				'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main',
				'DISP_EXIF_DATA'
			),
			'acp_cleanup'	=> array(
				'phpbbgallery/acpcleanup',
				'info_acp_gallery_cleanup',
				'adm/index.php?i=-phpbbgallery-acpcleanup-acp-main_module&mode=cleanup',
				'ACP_GALLERY_CLEANUP'
			),
			'acp_import'	=> array(
				'phpbbgallery/acpimport',
				'info_acp_gallery_acpimport',
				'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images',
				'ACP_IMPORT_ALBUMS'
			),
		);
	}
	/**
	* @dataProvider install_data
	*/
	public function test_install($ext, $lang, $path, $search)
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext($ext, $lang);
		$crawler = self::request('GET', $path . '&sid=' . $this->sid);
		$this->assertContainsLang($search, $crawler->text());

		$this->logout();
		$this->logout();
	}
	// Stop core so we can test if all works with all add-ons off
	public function togle_data()
	{
		return array(
			'core'	=> array('phpbbgallery/core'),
			'exif'	=> array('phpbbgallery/exif'),
			'acpcleanup'	=> array('phpbbgallery/acpcleanup'),
			'acpimport'	=> array('phpbbgallery/acpimport'),
		);
	}
	/**
	* @dataProvider togle_data
	*/
	public function togle_core($ext)
	{
		$this->get_db();
		if (strpos($this->db->get_sql_layer(), 'sqlite3') === 0)
		{
			$this->markTestSkipped('There seems to be issue with SQlite and travis about togling');
		}
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('common');
		$this->add_lang('acp/extensions');

		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=disable_pre&ext_name=phpbbgallery%2Fcore&sid=' . $this->sid);
		$form = $crawler->selectButton('disable')->form();
		$crawler = self::submit($form);
		$this->assertContainsLang('EXTENSION_DISABLE_SUCCESS', $crawler->filter('.successbox')->text());

		$this->assertEquals(0, $this->get_state($ext));

		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=enable_pre&ext_name=phpbbgallery%2Fcore&sid=' . $this->sid);
		$form = $crawler->selectButton('enable')->form();
		$crawler = self::submit($form);
		$this->assertContainsLang('EXTENSION_ENABLE_SUCCESS', $crawler->filter('.successbox')->text());

		$this->assertEquals(1, $this->get_state($ext));
	}
	// Let's test basic functionality
	public function test_basic_gallery_access()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_mcp');
		$this->add_lang('common');

		$crawler = self::request('GET', 'app.php/gallery');
		$this->assertContains($this->lang('NO_ALBUMS'), $crawler->text());
		$this->assertContains($this->lang('USERS_PERSONAL_ALBUMS'), $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/users');
		$this->assertContains($this->lang('NO_ALBUMS'), $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/moderate');
		$this->assertContains('You are not authorised to access this area', $crawler->text());

		$this->logout();
	}
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


		$this->logout();
		$this->logout();
	}
	public function test_acl_set_permissions_public()
	{
		$crawler = self::request('GET', 'app.php/gallery');
		$this->assertNotContains('First test album!', $crawler->text());

		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		// Let us set for administration
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['p_system'] = 0;
		$form['album_id'] = array(1);
		$crawler = self::submit($form);

		$this->assertContains('First test album!', $crawler->text());

		$form = $crawler->filter('form[id=add_groups]')->selectButton($this->lang('ADD_PERMISSIONS'))->form();
		$form['group_id'] = array(5);
		$crawler = self::submit($form);

		$this->assertContainsLang('PERMISSION_I_VIEW', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array(
			'setting'	=> array(
				1	=> array (
					5 => array(
						'a_list'			=> '1',
						'i_view'			=> '1',
						'i_watermark'		=> '1',
						'i_upload'			=> '1',
						'i_edit'			=> '1',
						'i_delete'			=> '1',
						'i_rate'			=> '1',
						'i_approve'			=> '1',
						'i_report'			=> '1',
						'i_count'			=> '0',
						'i_unlimited'		=> '1',
						'c_read'			=> '1',
						'c_post'			=> '1',
						'c_edit'			=> '1',
						'c_delete'			=> '1',
						'm_comments'		=> '1',
						'm_delete'			=> '1',
						'm_edit'			=> '1',
						'm_move'			=> '1',
						'm_report'			=> '1',
						'm_status'			=> '1',
					)
				)
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery');
		$this->assertContains('First test album!', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/album/1');
		$this->add_lang('common');

		$this->assertContainsLang('MCP_SHORT', $crawler->text());
		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());

		// Now let's set for registered users
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['p_system'] = 0;
		$form['album_id'] = array(1);
		$crawler = self::submit($form);

		$this->assertContains('First test album!', $crawler->text());

		$form = $crawler->filter('form[id=add_groups]')->selectButton($this->lang('ADD_PERMISSIONS'))->form();
		$form['group_id'] = array(2, 1);
		$crawler = self::submit($form);

		$this->assertContainsLang('PERMISSION_I_VIEW', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array(
			'setting'	=> array(
				1	=> array (
					2 => array(
						'a_list'			=> '1',
						'i_view'			=> '1',
						'i_watermark'		=> '0',
						'i_upload'			=> '1',
						'i_edit'			=> '1',
						'i_delete'			=> '1',
						'i_rate'			=> '1',
						'i_approve'			=> '0',
						'i_report'			=> '1',
						'i_count'			=> '0',
						'i_unlimited'		=> '1',
						'c_read'			=> '1',
						'c_post'			=> '1',
						'c_edit'			=> '1',
						'c_delete'			=> '1',
						'm_comments'		=> '0',
						'm_delete'			=> '0',
						'm_edit'			=> '0',
						'm_move'			=> '0',
						'm_report'			=> '0',
						'm_status'			=> '0',
					),
					1 => array(
						'a_list'			=> '1',
						'i_view'			=> '1',
						'i_watermark'		=> '0',
						'i_upload'			=> '1',
						'i_edit'			=> '1',
						'i_delete'			=> '1',
						'i_rate'			=> '1',
						'i_approve'			=> '0',
						'i_report'			=> '1',
						'i_count'			=> '0',
						'i_unlimited'		=> '1',
						'c_read'			=> '1',
						'c_post'			=> '1',
						'c_edit'			=> '1',
						'c_delete'			=> '1',
						'm_comments'		=> '0',
						'm_delete'			=> '0',
						'm_edit'			=> '0',
						'm_move'			=> '0',
						'm_report'			=> '0',
						'm_status'			=> '0',
					)
				)
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());

		$this->logout();
		$this->logout();

		$crawler = self::request('GET', 'app.php/gallery');
		$this->assertContains('First test album!', $crawler->text());

		$this->login('testuser1');

		$crawler = self::request('GET', 'app.php/gallery');
		$this->assertContains('First test album!', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$this->assertNotContainsLang('MCP', $crawler->text());
		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());

		$this->logout();
	}
	// Test MCP
	public function mcp_no_action_data()
	{
		return array(
			'overview_main'	=> array(
				'Main',
				'No images waiting for approval.',
				'app.php/gallery/moderate'
			),
			'waiting_main'	=> array(
				'Queue',
				'No images waiting for approval.',
				'app.php/gallery/moderate/approve'
			),
			'reports_open_main'	=> array(
				'Open reports',
				'The report could not be found.',
				'app.php/gallery/moderate/reports'
			),
			'reports_closed_main'	=> array(
				'Closed reports',
				'The report could not be found.',
				'app.php/gallery/moderate/reports_closed'
			),
			'moderator_log_main'	=> array(
				'Moderator logs',
				'No log entries.',
				'app.php/gallery/moderate/actions'
			),
		);
	}
	/**
	* @dataProvider mcp_no_action_data
	*/
	public function test_mcp_with_no_action($title, $message, $url)
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_mcp');
		$this->add_lang('common');

		$crawler = self::request('GET', $url);
		$this->assertContains($title, $crawler->text());
		$this->assertContains($message, $crawler->text());

		$this->logout();
	}
	public function test_acl_upload_public_admin()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('common');

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		//$link = $crawler->filter('div.upload-icon > a')->attr('href');
		//$this->assertContains('lalalalalal',  $crawler->filter('div.upload-icon > a')->attr('href'));

		$upload_url = substr($crawler->filter('a:contains("' . $this->lang('UPLOAD_IMAGE') . '")')->attr('href'), 1);

		$crawler = self::request('GET', $upload_url);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());

		$form = $crawler->selectButton($this->lang('CONTINUE'))->form();

		$form['files'] = array(__DIR__ . '/images/valid.jpg');
		$crawler = self::submit($form);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());

		//$this->assertContains('zazazazazaza', $crawler->text());
		$form = $crawler->selectButton($this->lang['SUBMIT'])->form();
		$form['image_name'] = array(
			0 => 'Valid',
		);
		$crawler = self::submit($form);

		$this->assertContainsLang('ALBUM_UPLOAD_SUCCESSFUL', $crawler->text());
		$this->assertNotContains('But your image must be approved by a administrator or a moderator before they are public visible.', $crawler->text());

		//$crawler = self::request('GET', 'app.php/gallery/album/1');
		$meta = $crawler->filter('meta[http-equiv="refresh"]')->attr('content');
		$this->assertContains('app.php/gallery/album/1', $meta);

		$url = $this->get_url_from_meta($meta);
		$crawler = self::request('GET', substr($url, 1));


		$this->assertContains('1 image',  $crawler->text());
		$this->assertContains('Valid',  $crawler->text());
		$this->logout();
	}
	public function test_acl_upload_public_user()
	{
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('common');

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$upload_url = substr($crawler->filter('a:contains("' . $this->lang('UPLOAD_IMAGE') . '")')->attr('href'), 1);
		/*
		// This is going to take some time to figure out how to do it as normal single test.
		$crawler = self::request('GET', $upload_url);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());

		$form = $crawler->selectButton($this->lang('CONTINUE'))->form();

		$form['files'] =  array(__DIR__ . '/images/valid.jpg', __DIR__ . '/images/valid.jpg');
		$crawler = self::submit($form);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());

		//$this->assertContains('zazazazazaza', $crawler->text());
		$form = $crawler->selectButton($this->lang['SUBMIT'])->form();
		$form['image_name'] = array(
			0 => 'Valid but needs approve',
			1 => 'Valid but needs delete',
		);
		$crawler = self::submit($form);

		$this->assertContains('But your image must be approved by a administrator or a moderator before they are public visible.', $crawler->text());
		*/
		$crawler = self::request('GET', $upload_url);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());

		$form = $crawler->selectButton($this->lang('CONTINUE'))->form();

		$form['files'] =  array(__DIR__ . '/images/valid.jpg');
		$crawler = self::submit($form);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());

		//$this->assertContains('zazazazazaza', $crawler->text());
		$form = $crawler->selectButton($this->lang['SUBMIT'])->form();
		$form['image_name'] = array(
			0 => 'Valid but needs approve',
		);
		$crawler = self::submit($form);

		$this->assertContains('But your image must be approved by a administrator or a moderator before they are public visible.', $crawler->text());
		//Second image ???
		$crawler = self::request('GET', $upload_url);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());

		$form = $crawler->selectButton($this->lang('CONTINUE'))->form();

		$form['files'] =  array(__DIR__ . '/images/valid.jpg');
		$crawler = self::submit($form);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());

		//$this->assertContains('zazazazazaza', $crawler->text());
		$form = $crawler->selectButton($this->lang['SUBMIT'])->form();
		$form['image_name'] = array(
			0 => 'Valid but needs delete',
		);
		$crawler = self::submit($form);

		$this->assertContains('But your image must be approved by a administrator or a moderator before they are public visible.', $crawler->text());
		//$crawler = self::request('GET', 'app.php/gallery/album/1');
		$meta = $crawler->filter('meta[http-equiv="refresh"]')->attr('content');
		$this->assertContains('app.php/gallery/album/1', $meta);

		$url = $this->get_url_from_meta($meta);
		$crawler = self::request('GET', substr($url, 1));

		$this->assertContains('1 image',  $crawler->text());
		$this->assertContains('Valid',  $crawler->text());

		$this->logout();
	}

	public function test_approve_image()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_mcp');
		$this->add_lang('common');

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$image = $crawler->filter('a:contains("Valid but needs approve")')->parents()->parents();

		$form = $image->selectButton($this->lang['APPROVE'])->form();
		$crawler = self::submit($form);

		$form = $crawler->selectButton($this->lang['YES'])->form();
		$crawler = self::submit($form);

		$this->assertContains('In total there is 1 image approved.',  $crawler->text());

		$this->logout();
	}

	public function test_disaprove_image()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_mcp');
		$this->add_lang('common');

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$image = $crawler->filter('a:contains("Valid but needs delete")')->parents()->parents();

		$form = $image->selectButton($this->lang('DISAPPROVE'))->form();
		$crawler = self::submit($form);


		$form = $crawler->selectButton($this->lang['YES'])->form();
		$crawler = self::submit($form);

		$this->assertContainsLang('DELETED_IMAGE',  $crawler->text());

		$this->logout();
	}

	public function test_visibility_user()
	{
		$this->login('testuser1');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('common');

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$this->assertContains('Valid',  $crawler->text());
		$this->assertContains('Valid but needs approve',  $crawler->text());

		$this->logout();
	}

	public function test_acl_set_permissions_own()
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		// Let us set for administration
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['p_system'] = -2;
		$crawler = self::submit($form);

		$this->assertContainsLang('OWN_PERSONAL_ALBUMS', $crawler->text());

		$form = $crawler->filter('form[id=add_groups]')->selectButton($this->lang('ADD_PERMISSIONS'))->form();
		$form['group_id'] = array(5);
		$crawler = self::submit($form);

		$this->assertContainsLang('OWN_PERSONAL_ALBUMS', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array(
			'setting'	=> array(
				-2	=> array (
					5 => array(
						'i_watermark'	=> '1',
						'i_upload'		=> '1',
						'i_approve'		=> '1',
						'i_edit'		=> '1',
						'i_delete'		=> '1',
						'i_report'		=> '1',
						'i_rate'		=> '1',
						'c_read'		=> '1',
						'c_post'		=> '1',
						'c_delete'		=> '1',
						'm_comments'	=> '1',
						'm_delete'		=> '1',
						'm_edit'		=> '1',
						'm_report'		=> '1',
						'm_status'		=> '1',
						'a_list'		=> '1',
						'i_count'		=> '0',
						'i_unlimited'	=> '1',
						'a_count'		=> '0',
						'a_unlimited'	=> '1',
						'a_restrict'	=> '1',
					)
				)
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());

		// Now let's set for registered users
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['p_system'] = -2;
		$crawler = self::submit($form);

		$this->assertContainsLang('OWN_PERSONAL_ALBUMS', $crawler->text());

		$form = $crawler->filter('form[id=add_groups]')->selectButton($this->lang('ADD_PERMISSIONS'))->form();
		$form['group_id'] = array(2);
		$crawler = self::submit($form);

		$this->assertContainsLang('OWN_PERSONAL_ALBUMS', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array(
			'setting'	=> array(
				-2	=> array (
					2 => array(
						'i_watermark'	=> '0',
						'i_upload'		=> '1',
						'i_approve'		=> '0',
						'i_edit'		=> '1',
						'i_delete'		=> '1',
						'i_report'		=> '1',
						'i_rate'		=> '1',
						'c_read'		=> '1',
						'c_post'		=> '1',
						'c_delete'		=> '1',
						'm_comments'	=> '0',
						'm_delete'		=> '0',
						'm_edit'		=> '0',
						'm_report'		=> '0',
						'm_status'		=> '0',
						'a_list'		=> '1',
						'i_count'		=> '0',
						'i_unlimited'	=> '1',
						'a_count'		=> '0',
						'a_unlimited'	=> '1',
						'a_restrict'	=> '1',
					)
				)
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());

		$this->logout();
		$this->logout();
	}

	public function test_acl_set_permissions_personal()
	{
		$this->login();
		$this->admin_login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		// Let us set for administration
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['p_system'] = -3;
		$crawler = self::submit($form);

		$this->assertContainsLang('PERSONAL_ALBUMS', $crawler->text());

		$form = $crawler->filter('form[id=add_groups]')->selectButton($this->lang('ADD_PERMISSIONS'))->form();
		$form['group_id'] = array(5);
		$crawler = self::submit($form);

		$this->assertContainsLang('PERSONAL_ALBUMS', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array(
			'setting'	=> array(
				-3	=> array (
					5 => array(
						'i_view'		=> '1',
						'i_watermark'	=> '1',
						'i_upload'		=> '1',
						'i_report'		=> '1',
						'i_rate'		=> '1',
						'c_read'		=> '1',
						'c_post'		=> '1',
						'c_edit'		=> '1',
						'c_delete'		=> '1',
						'm_comments'	=> '1',
						'm_delete'		=> '1',
						'm_edit'		=> '1',
						'm_move'		=> '1',
						'm_report'		=> '1',
						'm_status'		=> '1',
						'a_list'		=> '1',
					)
				)
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());

		// Now let's set for registered users
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['p_system'] = -3;
		$crawler = self::submit($form);

		$this->assertContainsLang('PERSONAL_ALBUMS', $crawler->text());

		$form = $crawler->filter('form[id=add_groups]')->selectButton($this->lang('ADD_PERMISSIONS'))->form();
		$form['group_id'] = array(2);
		$crawler = self::submit($form);

		$this->assertContainsLang('PERSONAL_ALBUMS', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array(
			'setting'	=> array(
				-3	=> array (
					2 => array(
						'i_view'		=> '1',
						'i_watermark'	=> '0',
						'i_upload'		=> '0',
						'i_report'		=> '1',
						'i_rate'		=> '1',
						'c_read'		=> '1',
						'c_post'		=> '1',
						'c_edit'		=> '1',
						'c_delete'		=> '1',
						'm_comments'	=> '0',
						'm_delete'		=> '0',
						'm_edit'		=> '0',
						'm_move'		=> '0',
						'm_report'		=> '0',
						'm_status'		=> '0',
						'a_list'		=> '1',
					)
				)
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());

		$this->logout();
		$this->logout();
	}

	public function test_init_personal_album()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_ucp');
		$this->add_lang('ucp');

		$crawler = self::request('GET', 'ucp.php?i=-phpbbgallery-core-ucp-main_module&mode=manage_albums&sid='  . $this->sid);

		$this->assertContains('You don’t have a personal album yet. Here you can create your personal album, with some subalbums.In personal albums only the owner can upload images', $crawler->text());

		$form = $crawler->selectButton($this->lang('CREATE_PERSONAL_ALBUM'))->form();
		$crawler = self::submit($form);

		$this->assertContainsLang('PERSONAL_ALBUM', $crawler->text());
		$this->assertContainsLang('NO_SUBALBUMS', $crawler->text());

		$this->logout();
	}

	public function test_create_subalbum_personal()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_ucp');
		$this->add_lang('ucp');

		$crawler = self::request('GET', 'ucp.php?i=-phpbbgallery-core-ucp-main_module&mode=manage_albums&sid='  . $this->sid);

		$form = $crawler->selectButton($this->lang('CREATE_SUBALBUM'))->form();
		$crawler = self::submit($form);

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['album_name'] = 'Personal user subalbum';
		$crawler = self::submit($form);

		$this->assertContainsLang('CREATED_SUBALBUM', $crawler->text());

		$upload_url = substr($crawler->filter('a:contains("'.$this->lang('BACK_TO_PREV').'")')->attr('href'), 1);
		$crawler = self::request('GET', $upload_url);

		$this->assertContainsLang('MANAGE_SUBALBUMS', $crawler->text());
		$this->assertContains('Personal user subalbum', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/users');
		$this->assertContains('admin', $crawler->filter('div.polaroid')->text());

		$this->logout();
	}

	public function test_create_subalbum_admin()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);

		// Step 1
		$form = $crawler->selectButton($this->lang('CREATE_ALBUM'))->form();
		$form['album_name'] = 'First sub test album!';
		$crawler = self::submit($form);

		// Step 2 - we should have reached a form for creating album_name
		$this->assertContainsLang('ALBUM_EDIT_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['parent_id'] = 1;
		$crawler = self::submit($form);

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$id = $crawler->filter('option:contains("First sub test album!")')->attr('value');

		$this->assertEquals(4, $id);

		// Let us set for administration
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['p_system'] = 0;
		$form['album_id'] = array($id);
		$crawler = self::submit($form);

		$this->assertContains('First sub test album!', $crawler->text());

		$form = $crawler->filter('form[id=add_groups]')->selectButton($this->lang('ADD_PERMISSIONS'))->form();
		$form['group_id'] = array(2, 5);
		$crawler = self::submit($form);

		$this->assertContains('First sub test album!', $crawler->text());
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array(
			'setting'	=> array(
				$id	=> array (
					2 => array(
						'a_list'			=> '1',
						'i_view'			=> '1',
						'i_watermark'		=> '0',
						'i_upload'			=> '1',
						'i_edit'			=> '1',
						'i_delete'			=> '1',
						'i_rate'			=> '1',
						'i_approve'			=> '0',
						'i_report'			=> '1',
						'i_count'			=> '0',
						'i_unlimited'		=> '1',
						'c_read'			=> '1',
						'c_post'			=> '1',
						'c_edit'			=> '1',
						'c_delete'			=> '1',
						'm_comments'		=> '0',
						'm_delete'			=> '0',
						'm_edit'			=> '0',
						'm_move'			=> '0',
						'm_report'			=> '0',
						'm_status'			=> '0',
					),
					5 => array(
						'a_list'			=> '1',
						'i_view'			=> '1',
						'i_watermark'		=> '1',
						'i_upload'			=> '1',
						'i_edit'			=> '1',
						'i_delete'			=> '1',
						'i_rate'			=> '1',
						'i_approve'			=> '1',
						'i_report'			=> '1',
						'i_count'			=> '0',
						'i_unlimited'		=> '1',
						'c_read'			=> '1',
						'c_post'			=> '1',
						'c_edit'			=> '1',
						'c_delete'			=> '1',
						'm_comments'		=> '1',
						'm_delete'			=> '1',
						'm_edit'			=> '1',
						'm_move'			=> '1',
						'm_report'			=> '1',
						'm_status'			=> '1',
					)
				),
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());

		$this->logout();
		$this->logout();

		$this->login('testuser1');
		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$this->assertContains('First sub test album!', $crawler->text());

		$this->logout();
	}
	public function test_upload_to_public_subalbum()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');

		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$link = $crawler->filter('div.polaroid')->eq(0)->filter('a')->eq(0)->attr('href');
		//$this->assertContains('zzzazazazaza', substr($link, 1));
		$crawler = self::request('GET', substr($link, 1));

		$upload_url = substr($crawler->filter('a:contains("' . $this->lang('UPLOAD_IMAGE') . '")')->attr('href'), 1);
		$crawler = self::request('GET', $upload_url);

		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$form = $crawler->selectButton($this->lang('CONTINUE'))->form();
		$form['files'] = array(__DIR__ . '/images/valid.jpg');
		$crawler = self::submit($form);

		$form = $crawler->selectButton('submit')->form();
		$form['image_name'] = array(
			0 => 'Image in sublabum to move',
		);
		$crawler = self::submit($form);

		$this->assertContainsLang('ALBUM_UPLOAD_SUCCESSFUL', $crawler->text());

		$crawler = self::request('GET', substr($link, 1));
		$this->assertContains('Image in sublabum to move', $crawler->text());

		$this->logout();
	}
	public function test_acp_copy_permissions()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);

		// Step 1
		$form = $crawler->selectButton($this->lang('CREATE_ALBUM'))->form();
		$form['album_name'] = 'Second subalbum!';
		$crawler = self::submit($form);

		// Step 2 - we should have reached a form for creating album_name
		$this->assertContainsLang('ALBUM_EDIT_EXPLAIN', $crawler->text());

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['parent_id'] = 1;
		$crawler = self::submit($form);

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=copy&sid=' . $this->sid);
		$album = $crawler->filter('select#dest_albums')->filter('option:contains("Second subalbum!")')->attr('value');
		$form = $crawler->selectButton('submit')->form();
		$form['src_album_id'] = 1;
		$form['dest_album_ids'] = array($album);
		$crawler = self::submit($form);

		$form = $crawler->selectButton('confirm')->form();
		$crawler = self::submit($form);

		$this->assertContainsLang('COPY_PERMISSIONS_SUCCESSFUL', $crawler->text());
		$this->logout();
		$this->logout();

	}
	public function test_create_album_copy_permissions()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);

		// Step 1
		$form = $crawler->selectButton($this->lang('CREATE_ALBUM'))->form();
		$form['album_name'] = 'First subalbum subalbum!';
		$crawler = self::submit($form);

		// Step 2 - we should have reached a form for creating album_name
		$this->assertContainsLang('ALBUM_EDIT_EXPLAIN', $crawler->text());

		$album = $crawler->filter('select#parent_id')->filter('option:contains("First sub test album!")')->attr('value');
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['parent_id'] = $album;
		$form['album_perm_from'] = $album;
		$crawler = self::submit($form);

		$this->assertContainsLang('ALBUM_CREATED', $crawler->text());

		$this->logout();
		$this->logout();
	}
	public function test_a_list_permissions()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);

		// Step 1
		$form = $crawler->selectButton($this->lang('CREATE_ALBUM'))->form();
		$form['album_name'] = 'Admins see only!';
		$crawler = self::submit($form);
		// Step 2 - we should have reached a form for creating album_name
		$this->assertContainsLang('ALBUM_EDIT_EXPLAIN', $crawler->text());

		$album = $crawler->filter('select#parent_id')->filter('option:contains("First sub test album!")')->attr('value');
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['album_perm_from'] = $album;
		$crawler = self::submit($form);

		$this->assertContainsLang('ALBUM_CREATED', $crawler->text());

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-permissions_module&mode=manage&sid='  . $this->sid);
		$this->assertContainsLang('PERMISSIONS_EXPLAIN', $crawler->text());

		$id = $crawler->filter('option:contains("Admins see only!")')->attr('value');

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['p_system'] = 0;
		$form['album_id'] = array($id);
		$crawler = self::submit($form);

		$this->assertContains('Admins see only!', $crawler->text());

		$form = $crawler->filter('form[id=groups]')->selectButton($this->lang('EDIT_PERMISSIONS'))->form();
		$form['group_id'] = array(2);
		$crawler = self::submit($form);

		$this->assertContains('Admins see only!', $crawler->text());
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array(
			'setting'	=> array(
				$id	=> array (
					2 => array(
						'a_list'			=> '0',
					),
				)
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());

		$this->logout();
		$this->logout();

		$crawler = self::request('GET', 'app.php/gallery');

		$this->assertNotContains('Admins see only!', $crawler->text());

		$this->login('testuser1');
		$crawler = self::request('GET', 'app.php/gallery');

		$this->assertNotContains('Admins see only!', $crawler->text());
		$this->logout();

		$this->login();
		$crawler = self::request('GET', 'app.php/gallery');

		$this->assertContains('Admins see only!', $crawler->text());
		$this->logout();

	}

	public function test_delete_album_move_images_and_subalbums()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);

		// Step 1 - see subalbums
		$url = $crawler->filter('a:contains("First test album!")')->attr('href');
		$crawler = self::request('GET', substr($url, 5));

		$url = $crawler->filter('a:contains("First sub test album!")')->parents()->parents()->filter('td')->eq(2)->filter('a')->eq(3)->attr('href');
		$crawler = self::request('GET', substr($url, 5));

		$album = $crawler->filter('select#images_to_id')->filter('option:contains("Second subalbum!")')->attr('value');
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['action_images'] = 'move';
		$form['images_to_id'] = $album;
		$form['action_subalbums'] = 'move';
		$form['subalbums_to_id'] = $album;
		$crawler = self::submit($form);

		$this->assertContainsLang('ALBUM_DELETED', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/album/' . $album);

		$this->assertContains('First subalbum subalbum!', $crawler->text());
		$this->assertContains('Image in sublabum to move', $crawler->text());
		$this->assertEquals(2, $crawler->filter('div.polaroid')->count());

		$this->logout();
		$this->logout();
	}
	public function test_delete_album_move_images_and_delete_subalbums()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);

		// Step 1 - see subalbums
		$url = $crawler->filter('a:contains("First test album!")')->attr('href');
		$crawler = self::request('GET', substr($url, 5));

		$url = $crawler->filter('a:contains("Second subalbum!")')->parents()->parents()->filter('td')->eq(2)->filter('a')->eq(2)->attr('href');
		$crawler = self::request('GET', substr($url, 5));

		$album = $crawler->filter('select#images_to_id')->filter('option:contains("First test album!")')->attr('value');
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['action_images'] = 'move';
		$form['images_to_id'] = $album;
		$form['action_subalbums'] = 'delete';
		$crawler = self::submit($form);

		$this->assertContainsLang('ALBUM_DELETED', $crawler->text());

		$crawler = self::request('GET', 'app.php/gallery/album/' . $album);

		$this->assertNotContains('First subalbum subalbum!', $crawler->text());
		$this->assertContains('Image in sublabum to move', $crawler->text());
		$this->assertEquals(3, $crawler->filter('div.polaroid')->count());

		$this->logout();
		$this->logout();
	}
	public function test_edit_albums_admin()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/permissions');

		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-albums_module&mode=manage&sid=' . $this->sid);

		$object = $crawler->filter('a:contains("First test album")')->parents()->parents();
		$edit = $object->filter('img[title=Edit]')->parents()->attr('href');

		//$this->assertContains('zazazaza', $edit);
		$crawler = self::request('GET', substr($edit, 5));

		$this->assertContainsLang('ALBUM_EDIT_EXPLAIN', $crawler->text());
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['album_watermark'] = 0;
		$crawler = self::submit($form);

		$this->assertContains('Album has been updated successfully.', $crawler->text());

		$this->logout();
		$this->logout();
	}
	public function log_data()
	{
		return array(
			'all'	=> array(
				'all',
				11
			),
			'admin'	=> array(
				'admin',
				9
			),
			'moderator'	=> array(
				'moderator',
				3,
			),
			'system' => array(
				'system',
				false
			)
		);
	}
	/**
	* @dataProvider log_data
	*/
	public function test_log($type, $test)
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/core', 'info_acp_gallery_logs');
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('acp/common');


		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-gallery_logs_module&mode=main&sid=' . $this->sid);

		$form = $crawler->selectButton('filter')->form();
		$form['lf'] = $type;
		$crawler = self::submit($form);

		if ($test)
		{
			$table = $crawler->filter('table')->filter('tr')->count();
			$this->assertEquals($test, $table);
		}
		else
		{
			$this->assertContainsLang('NO_ENTRIES', $crawler->text());
		}
	}
}