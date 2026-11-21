<?php
/**
 * phpBB Gallery - Core Extension
 *
 * @package   phpbbgallery/core
 * @copyright 2014- satanasov, 2018- Leinad4Mind
 * @license   GPL-2.0-only
 */

namespace phpbbgallery\tests\core;

/**
 * @group core
 */
class core_log_json_test extends core_base
{
	/** @var \phpbbgallery\core\auth\auth */
	protected $gallery_auth;

	/** @var \phpbbgallery\core\log */
	protected $gallery_log;

	public function setUp(): void
	{
		parent::setUp();

		$this->gallery_auth = $this->getMockBuilder('\phpbbgallery\core\auth\auth')
			->disableOriginalConstructor()
			->getMock();
		$this->gallery_auth->method('acl_album_ids')->willReturn(array(1, 2));

		$this->gallery_log = new \phpbbgallery\core\log(
			$this->db,
			$this->user,
			$this->language,
			$this->user_loader,
			$this->template,
			$this->controller_helper,
			$this->pagination,
			$this->gallery_auth,
			$this->gallery_config,
			'phpbb_gallery_log',
			'phpbb_gallery_images'
		);

		$this->db->sql_query('DELETE FROM phpbb_gallery_log');
	}

	public function test_log_description_round_trip_preserves_json_characters()
	{
		$this->user->data['user_id'] = 2;
		$this->user->ip = '127.0.0.1';
		$description = array('LOG_TEST', 'C:\\gallery\\image.jpg', '"quoted"', 'caf?');

		$this->gallery_log->add_log('system', 'test', 0, 0, $description);

		$sql = 'SELECT description
			FROM phpbb_gallery_log';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$this->assertSame($description, json_decode($row['description'], true));
	}

	public function test_log_list_decodes_description_without_slash_mutation()
	{
		$this->user->data['user_id'] = 2;
		$this->user->ip = '127.0.0.1';
		$description = array('LOG_TEST', 'C:\\gallery\\image.jpg');
		$this->gallery_log->add_log('system', 'test', 0, 0, $description);

		$this->template->expects($this->once())
			->method('assign_block_vars')
			->with('log', $this->callback(function ($row)
			{
				return $row['U_LOG_ACTION'] === 'LOG_TEST';
			}));

		$this->gallery_log->build_list('system', 10, 1, -1);
	}
}
