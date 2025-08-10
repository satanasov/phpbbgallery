<?php
/**
 * phpBB Gallery - Fix EXIF Field Migration
 *
 * @package   phpbbgallery/exif
 * @author    Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\exif\migrations;

use phpbb\db\migration\migration;

class m2_fix_exif_field extends migration
{
    public static function depends_on(): array
    {
        return [
            '\phpbbgallery\exif\migrations\m1_init',
        ];
    }

    public function update_schema(): array
    {
        return [
            'change_columns' => [
                $this->table_prefix . 'gallery_images' => [
                    'image_exif_data' => ['TEXT', null],
                ],
            ],
        ];
    }

    public function revert_schema(): array
    {
        return [
            'change_columns' => [
                $this->table_prefix . 'gallery_images' => [
                    'image_exif_data' => ['TEXT', ''],
                ],
            ],
        ];
    }
}
