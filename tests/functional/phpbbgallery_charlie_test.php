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
			'pf_phpbb_facebook'	=> 'test',
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
	public function show_link_data()
	{
		return array(
			'yes'	=> array(
				1,
				1
			),
			'no'	=> array(
				0,
				0
			),
			'reset'	=> array(
				1,
				1
			),
		);
	}
	/**
	* @dataProvider show_link_data
	*/
	public function test_contact_link($state, $count)
	{
		$this->login();
		$this->add_lang_ext('phpbbgallery/core', 'gallery');
		$this->add_lang_ext('phpbbgallery/core', 'gallery_ucp');
		$this->add_lang('ucp');
		
		$this->set_option('profile_pega', $state);
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		$this->assertEquals($count, $crawler->filter('a:contains("Visit user gallery")')->count());
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('testuser1') . '&sid=' . $this->sid);
		$this->assertEquals(0, $crawler->filter('a:contains("Visit user gallery")')->count());
	}
}