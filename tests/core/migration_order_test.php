<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @copyright 2014 nickvergessen, 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\tests\core;

require_once __DIR__ . '/../../core/migrations/release_1_2_0.php';
require_once __DIR__ . '/../../core/migrations/release_1_2_0_db_create.php';
require_once __DIR__ . '/../../core/migrations/release_1_2_0_add_bbcode.php';
require_once __DIR__ . '/../../core/migrations/release_1_2_0_create_filesystem.php';
require_once __DIR__ . '/../../core/migrations/split_ucp_module_settings.php';
require_once __DIR__ . '/../../core/migrations/release_3_2_1_0.php';
require_once __DIR__ . '/../../core/migrations/release_3_2_1_1.php';
require_once __DIR__ . '/../../core/migrations/release_3_3_0.php';
require_once __DIR__ . '/../../core/migrations/release_3_4_0.php';
require_once __DIR__ . '/../../core/migrations/remove_gallery_version.php';
require_once __DIR__ . '/../../core/migrations/create_import_dir.php';

class migration_order_test extends \phpbb_test_case
{
	public function test_upgrade_migrations_form_a_linear_chain()
	{
		$this->assertSame(
			array('\phpbbgallery\core\migrations\release_1_2_0'),
			\phpbbgallery\core\migrations\release_1_2_0_db_create::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\release_1_2_0_db_create'),
			\phpbbgallery\core\migrations\release_1_2_0_add_bbcode::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\release_1_2_0_add_bbcode'),
			\phpbbgallery\core\migrations\release_1_2_0_create_filesystem::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\release_1_2_0_create_filesystem'),
			\phpbbgallery\core\migrations\split_ucp_module_settings::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\split_ucp_module_settings'),
			\phpbbgallery\core\migrations\release_3_2_1_0::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\release_3_2_1_0'),
			\phpbbgallery\core\migrations\release_3_2_1_1::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\release_3_2_1_1'),
			\phpbbgallery\core\migrations\release_3_3_0::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\release_3_3_0'),
			\phpbbgallery\core\migrations\release_3_4_0::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\release_3_4_0'),
			\phpbbgallery\core\migrations\remove_gallery_version::depends_on()
		);
		$this->assertSame(
			array('\phpbbgallery\core\migrations\remove_gallery_version'),
			\phpbbgallery\core\migrations\create_import_dir::depends_on()
		);
	}

	public function test_upgrade_migration_persists_webp_default()
	{
		$this->assertArrayHasKey('allow_webp', \phpbbgallery\core\migrations\release_3_2_1_0::$configs);
		$this->assertTrue(\phpbbgallery\core\migrations\release_3_2_1_0::$configs['allow_webp']);
	}
}
