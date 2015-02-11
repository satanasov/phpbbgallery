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
	public function test_stop_core($ext)
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
		
		$this->assertEquals(0, $this->get_state($ext));
		
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=enable_pre&ext_name=phpbbgallery%2Fcore&sid=' . $this->sid);
		$form = $crawler->selectButton($this->lang('ENABLE'))->form();
		$crawler = self::submit($form);
		
		$this->assertContainsLang('EXTENSION_UNABLE_SUCCESS', $crawler->text());

	}
}