<?php
/**
*
* @package phpBB Gallery Core
* @copyright (c) 2014 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\core\migrations;

class release_1_2_0_add_bbcode extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_2_0');
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
		$sql = 'SELECT bbcode_id FROM ' . $this->table_prefix . 'bbcodes WHERE LOWER(bbcode_tag) = \'image\'';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			// Create new BBCode
			$sql = 'SELECT MAX(bbcode_id) AS max_bbcode_id FROM ' . $this->table_prefix . 'bbcodes';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				$bbcode_id = $row['max_bbcode_id'] + 1;
				// Make sure it is greater than the core BBCode ids...
				if ($bbcode_id <= NUM_CORE_BBCODES)
				{
					$bbcode_id = NUM_CORE_BBCODES + 1;
				}
			}
			else
			{
				$bbcode_id = NUM_CORE_BBCODES + 1;
			}

			$url = generate_board_url() . '/';
			if ($this->config['enable_mod_rewrite'])
			{
				$url .= 'gallery/image/';
			}
			else
			{
				$url .= 'app.php/gallery/image/';
			}
			if ($bbcode_id <= BBCODE_LIMIT)
			{
				$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'bbcodes ' . $this->db->sql_build_array(
					'INSERT',
					array(
						'bbcode_tag'			=> 'image',
						'bbcode_id'				=> (int) $bbcode_id,
						'bbcode_helpline'		=> 'GALLERY_HELPLINE_ALBUM',
						'display_on_posting'	=> 1,
						'bbcode_match'			=> '[image]{NUMBER}[/image]',
						'bbcode_tpl'			=> '<a href="' . $url . '{NUMBER}"><img src="' . $url . '{NUMBER}/mini" alt="{NUMBER}" /></a>',
						'first_pass_match'		=> '!\[image\]([0-9]+)\[/image\]!i',
						'first_pass_replace'	=> '[image:$uid]${1}[/image:$uid]',
						'second_pass_match'		=> '!\[image:$uid\]([0-9]+)\[/image:$uid\]!s',
						'second_pass_replace'	=> '<a href="' . $url . '${1}"><img src="' . $url . '${1}/mini" alt="${1}" /></a>'
					)
				));
			}
		}
	}
	public function remove_bbcode()
	{
		$sql = 'DELETE FROM ' . BBCODES_TABLE . ' WHERE bbcode_tag = \'image\'';
		$this->db->sql_query($sql);
	}
}
