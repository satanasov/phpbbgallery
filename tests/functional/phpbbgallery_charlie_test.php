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
class phpbbgallery_charlie_test extends phpbbgallery_base
{
	//We need to init the cpfs
	public function test_init_cpfs()
	{
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_ucp');
		$this->add_lang('ucp');

		$this->login();
		$crawler = self::request('GET', 'ucp.php?i=ucp_profile&mode=profile_info&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'pf_phpbb_location'	=> 'test',
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('PROFILE_UPDATED', $crawler->text());
		$this->logout();
		
		//testuser1
		$this->login('testuser1');
		$crawler = self::request('GET', 'ucp.php?i=ucp_profile&mode=profile_info&sid=' . $this->sid);
		$form = $crawler->selectButton('submit')->form();
		$form->setValues(array(
			'pf_phpbb_location'	=> 'test',
		));
		$crawler = self::submit($form);
		// Should be updated
		$this->assertContainsLang('PROFILE_UPDATED', $crawler->text());
		$this->logout();
	}
}