<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\core\migrations;

class release_3_2_1_1 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_2_0_db_create');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('phpbb_gallery_version', '3.2.2'))
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'gallery_users'	=> array(
					'rrc_zebra'		=> array('UINT:1', 0),
				),
			)
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'gallery_users' => array(
					'rrc_zebra',
				),
			),
		);
	}

}
