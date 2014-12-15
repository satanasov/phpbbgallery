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
class phpbbgallery_acp_test extends phpbbgallery_base
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
		$this->add_user_group('NEWLY_REGISTERED', array('testuser1'));
		
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
		
		$this->assertContainsLang('MCP', $crawler->text());
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
		
		// Let's build photo objects
		
		$photo1 = new UploadedFile(
			'/tmp/photo1.jpg',
			'photo1.jpg',
			'image/jpeg',
			512
		);
		
		$photo2 = new UploadedFile(
			'/tmp/photo2.jpg',
			'photo2.jpg',
			'image/jpeg',
			1024
		);
		
		$crawler = self::request('GET', 'app.php/gallery/album/1');
		
		$link = $crawler->selectLink($this->lang['UPLOAD_IMAGE'])->link();
		
		$crawler = self::request('GET', $link-attr('href'));
		
		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		
		$form = $crawler->selectButton($this->lang('CONTINUE'))->form();
		
		$this->upload_file('valid.jpg', 'image/jpeg', $form['action']);
		
		$this->assertContainsLang('UPLOAD_IMAGE', $crawler->text());
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$crawler = self::submit($form);
		
		$this->assertContainsLang('ALBUM_UPLOAD_SUCCESSFUL', $crawler->text());
		
		$crawler = self::request('GET', 'app.php/gallery/album/1');
		
		$this->assertContains('valid',  $crawler->filter('div.polaroid')->filter('p')->text());
		
		$this->logout();
	}
}