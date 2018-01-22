<?php



namespace phpbbgallery\core\migrations;


class release_3_2_1_1 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		error_log('this is shit');
		return array('\phpbbgallery\core\migrations\release_1_2_0_db_create');
	}

}