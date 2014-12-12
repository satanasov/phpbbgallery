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
/*
	public function install_bbcode()
	{
		// Load the acp_bbcode class
		if (!class_exists('acp_bbcodes'))
		{
			include($this->phpbb_root_path . 'includes/acp/acp_bbcodes.' . $this->php_ext);
		}
		$bbcode_tool = new \acp_bbcodes();

		$bbcode_data = array(
			'album' => array(
				'bbcode_helpline'	=> 'GALLERY_HELPLINE_ALBUM',
				'bbcode_match'		=> '[album]{NUMBER}[/album]',
				'bbcode_tpl'		=> '<a href="gallery/image/{NUMBER}"><img src="gallery/image/{NUMBER}/mini" alt="{NUMBER}" /></a>',
			),
		);
		foreach ($bbcode_data as $bbcode_name => $bbcode_array)
		{
			// Build the BBCodes
			$data = $bbcode_tool->build_regexp($bbcode_array['bbcode_match'], $bbcode_array['bbcode_tpl']);
			$bbcode_array += array(
				'bbcode_tag'			=> $data['bbcode_tag'],
				'first_pass_match'		=> $data['first_pass_match'],
				'first_pass_replace'	=> $data['first_pass_replace'],
				'second_pass_match'		=> $data['second_pass_match'],
				'second_pass_replace'	=> $data['second_pass_replace']
			);
			$sql = 'SELECT bbcode_id
				FROM ' . $this->table_prefix . "bbcodes
				WHERE LOWER(bbcode_tag) = '" . strtolower($bbcode_name) . "'
				OR LOWER(bbcode_tag) = '" . strtolower($bbcode_array['bbcode_tag']) . "'";
			$result = $this->db->sql_query($sql);
			$row_exists = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if ($row_exists)
			{
				// Update exisiting BBCode
				$bbcode_id = $row_exists['bbcode_id'];
				$sql = 'UPDATE ' . $this->table_prefix . 'bbcodes
					SET ' . $this->db->sql_build_array('UPDATE', $bbcode_array) . '
					WHERE bbcode_id = ' . $bbcode_id;
				$this->db->sql_query($sql);
			}
			else
			{
				// Create new BBCode
				$sql = 'SELECT MAX(bbcode_id) AS max_bbcode_id
					FROM ' . $this->table_prefix . 'bbcodes';
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
				if ($bbcode_id <= BBCODE_LIMIT)
				{
					$bbcode_array['bbcode_id'] = (int) $bbcode_id;
					$bbcode_array['display_on_posting'] = 1;
					$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'bbcodes ' . $this->db->sql_build_array('INSERT', $bbcode_array));
				}
			}
		}
	}
*/
	public function install_bbcode()
	{
		$sql = 'SELECT bbcode_id FROM ' . $this->table_prefix . 'bbcodes WHERE LOWER(bbcode_tag) = \'album\'';
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
			
			if ($bbcode_id <= BBCODE_LIMIT)
			{
				$this->db->sql_query('INSERT INTO ' . $this->table_prefix . 'bbcodes ' . $this->db->sql_build_array(
					'INSERT',
					array(
						'bbcode_tag'			=> 'album',
						'bbcode_id'				=> (int) $bbcode_id,
						'bbcode_helpline'		=> 'GALLERY_HELPLINE_ALBUM',
						'display_on_posting'	=> 0,
						'bbcode_match'			=> '[album]{NUMBER}[/album]',
						'bbcode_tpl'			=> '<a href="gallery/image/{NUMBER}"><img src="gallery/image/{NUMBER}/mini" alt="{NUMBER}" /></a>',
						'first_pass_match'		=> '!\[album\]([0-9]+)\[/album\]!i',
						'first_pass_replace'	=> '[album:$uid]${1}[/album:$uid]',
						'second_pass_match'		=> '!\[album:$uid\]([0-9]+)\[/album:$uid\]!s',
						'second_pass_replace'	=> '<a href="/gallery/image/${1}"><img src="/gallery/image/${1}/mini" alt="${1}" /></a>'
					)
				));
			}
		}
	}
	public function remove_bbcode()
	{
		$sql = 'DELETE FROM ' . BBCODES_TABLE . ' WHERE bbcode_tag = \'album\'';
		$this->db->sql_query($sql);
	}
}
