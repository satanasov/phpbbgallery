<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    Leinad4Mind
 * @copyright 2026 Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\core\migrations;

use phpbb\db\migration\migration;

/**
 * Members no longer see the personal album profile field in the UCP profile form, so saving the
 * form cannot empty it. The Gallery writes the field itself when the personal album is created.
 */
class hide_personal_album_profile_field extends migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\create_import_dir');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'set_show_profile'), array(0))),
		);
	}

	public function revert_data()
	{
		return array(
			array('custom', array(array($this, 'set_show_profile'), array(1))),
		);
	}

	public function set_show_profile($show_profile)
	{
		$sql = 'UPDATE ' . $this->table_prefix . 'profile_fields
			SET field_show_profile = ' . (int) $show_profile . "
			WHERE field_name = 'gallery_palbum'";
		$this->db->sql_query($sql);
	}
}
