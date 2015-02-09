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
class phpbbgallery_alpha_test extends phpbbgallery_base
{
	public function test_install()
	{
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-main_module&mode=overview&sid=' . $this->sid);
		$this->assertContainsLang('ACP_GALLERY_OVERVIEW_EXPLAIN', $crawler->text());
		
		
		// Let us create a user we will use for tests
		$this->create_user('testuser1');
		$this->add_user_group('REGISTERED', array('testuser1'));
		// Let me get admin out of registered
		$this->remove_user_group('REGISTERED', array('admin'));
		
		$this->logout();
		$this->logout();
	}
	
	public function test_exif_install()
	{
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/exif', 'exif');
		
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-core-acp-config_module&mode=main&sid=' . $this->sid);
		
		$this->assertContainsLang('DISP_EXIF_DATA', $crawler->text());
		
		$this->logout();
		$this->logout();
	}
	
	public function test_acpimport_install()
	{
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('phpbbgallery/core', 'gallery_acp');
		$this->add_lang_ext('phpbbgallery/acpimport', 'info_acp_gallery_acpimport');
		
		$crawler = self::request('GET', 'adm/index.php?i=-phpbbgallery-acpimport-acp-main_module&mode=import_images&sid=' . $this->sid);
		
		$this->assertContainsLang('ACP_IMPORT_ALBUMS', $crawler->text());
		
		$this->logout();
		$this->logout();
	}
	
	public function test_admin_create_album()
	{
		$this->login();
		$this->admin_login();
		
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
	
	public function test_acl_set_permissions_public()
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
		$form['group_id'] = array(2);
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
					)
				)
			)
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContainsLang('PERMISSIONS_STORED', $crawler->text());
		
		$this->logout();
		$this->logout();
		
		$this->login('testuser1');
		$crawler = self::request('GET', 'app.php/gallery/album/1');

		$this->assertNotContainsLang('MCP', $crawler->text());
		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		
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
		
		$upload_url = substr($crawler->filter('div.upload-icon > a')->attr('href'), 1);
		
		$crawler = self::request('GET', $upload_url);
		
		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());
		
		$form = $crawler->selectButton($this->lang('CONTINUE'))->form();
		
		$form['image_file_0'] =  __DIR__ . '/images/valid.jpg';;
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
		
		$crawler = self::request('GET', 'app.php/gallery/album/1');
		
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
		
		$upload_url = substr($crawler->filter('div.upload-icon > a')->attr('href'), 1);
		
		$crawler = self::request('GET', $upload_url);
		
		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		$this->assertContains('First test album!', $crawler->text());
		
		$form = $crawler->selectButton($this->lang('CONTINUE'))->form();
		
		$form['image_file_0'] =  __DIR__ . '/images/valid.jpg';;
		$form['image_file_1'] =  __DIR__ . '/images/valid.jpg';;
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
		
		$crawler = self::request('GET', 'app.php/gallery/album/1');
		
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
	
	public function test_create_subalbum_user()
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
	public function test_manage_albums_admin()
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
	
	public function test_image_on_image_page()
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang('common');
		
		// Test image
		$this->config_set('link_imagepag', 'image');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		$link = $crawler->filter('div.post')->eq(0)->filter('a')->attr('href');
		$this->assertContains('/source', $link);
		
		$this->config_set('link_imagepag', 'next');
		$crawler = self::request('GET', 'app.php/gallery/image/1');
		$link = $crawler->filter('div.post')->eq(0)->filter('a')->attr('href');
		$this->assertContains('gallery/image/', $link);
		
		// Test none
		//$this->config_set('link_imagepag', 'none');
		//$crawler = self::request('GET', 'app.php/gallery/image/1');
		//$object = $crawler->filter('div.post')->eq(0);
		//$link = $crawler->filter('div.post')->eq(0)->filter('a')->attr('href');
		//$this->assertContains('zazazazaza', $link);
		
		$this->logout();
	}
}