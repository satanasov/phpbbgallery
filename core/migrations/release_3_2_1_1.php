<?php



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
			array('config.update', array('phpbb_gallery_version', '3.2.1.1'))
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

}