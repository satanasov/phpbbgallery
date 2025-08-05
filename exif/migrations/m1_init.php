<?php
/**
 * phpBB Gallery - ACP Exif Extension
 *
 * @package   phpbbgallery/exif
 * @author    nickvergessen
 * @author    satanasov
 * @author    Leinad4Mind
 * @copyright 2007-2012 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\exif\migrations;

use phpbb\db\migration\migration;

class m1_init extends migration
{
	public static function depends_on(): array
	{
		return ['\phpbbgallery\core\migrations\release_1_2_0'];
	}

	public function update_data(): array
	{
		return [
			['config.add', ['phpbb_gallery_disp_exifdata', 1]],
		];
	}

	// Let's create the needed table
	public function update_schema(): array
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'gallery_images' => [
					'image_has_exif'   => ['UINT:3', 2],
					'image_exif_data'  => ['TEXT', ''],
				],
				$this->table_prefix . 'gallery_users' => [
					'user_viewexif'    => ['UINT:1', 0],
				],
			],
		];
	}

	public function revert_schema(): array
	{
		return [
			'drop_columns' => [
				$this->table_prefix . 'gallery_images' => [
					'image_has_exif',
					'image_exif_data',
				],
				$this->table_prefix . 'gallery_users' => [
					'user_viewexif',
				],
			],
		];
	}
}
