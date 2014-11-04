<?php
/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\migrations;

class m2_add_bbcode extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_1_6');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array(&$this, 'install_bbcode'))),
		);
	}
	
	public function revert_data()
	{
		return array(
			array('custom', array(array(&$this, 'remove_bbcode'))),
		);
	}
	
	public function install_bbcode()
	{
		$sql_array = array(
			'bbcode_tag'	=> 'album',
			'bbcode_helpline'	=> 'GALLERY_HELPLINE_ALBUM',
			'display_on_posting'	=> 1,
			'bbcode_match'	=> '[album]{NUMBER}[/album]',
			'bbcode_tpl'	=> '<a href="/gallery/image/{NUMBER}"><img src="/gallery/image/{NUMBER}/mini" alt="{NUMBER}" /></a>',
			'first_pass_match'	=> '!\[album\]([0-9]+)\[/album\]!i',
			'first_pass_replace'	=> '[album:$uid]${1}[/album:$uid]',
			'second_pass_match'	=> '!\[album:$uid\]([0-9]+)\[/album:$uid\]!s',
			'second_pass_replace'	=> '<a href="/gallery/image/${1}"><img src="/gallery/image/${1}/mini" alt="${1}" /></a>',
		);
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $this->db->sql_build_array('INSERT', $sql_array);
		$this->db->sql_query($sql);
	}
	
	public function remove_bbcode()
	{
		$sql = 'DELETE FROM ' . BBCODES_TABLE . ' WHERE bbcode_tag = \'album\'';
		$this->db->sql_query($sql);
	}
}
