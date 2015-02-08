<?php
/**
*
* @package phpBB Gallery EXIF
* @copyright (c) 2014 satanasov
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbgallery\exif\migrations;

class m1_init extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbbgallery\core\migrations\release_1_2_0');
	}

	public function update_data()
	{
		return array(
			// add config
			array('config.add', array('phpbb_gallery_disp_exifdata', 1))
		);
	}
	//lets create the needed table
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'gallery_images'	=> array(
					'image_has_exif'		=> array('UINT:3', 2),
					'image_exif_data'		=> array('TEXT', ''),
				),
				$this->table_prefix . 'gallery_users'	=> array(
					'user_viewexif'		=> array('UINT:1', 0),
				),
			),
		);
	}
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'gallery_images'	=> array(
					'image_has_exif',
					'image_exif_data'
				),
				$this->table_prefix . 'gallery_users'	=> array(
					'user_viewexif',
				),
			),
		);
	}
}
