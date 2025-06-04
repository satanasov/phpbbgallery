<?php
/**
* @package phpBB Gallery Test
* @copyright (c) 2025
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace phpbbgallery\tests\core;

use phpbbgallery\core\report;
use PHPUnit\Framework\TestCase;

class core_report_test extends TestCase
{
    protected $report;
    protected $gallery_log;
    protected $gallery_auth;
    protected $user;
    protected $language;
    protected $db;
    protected $user_loader;
    protected $album;
    protected $template;
    protected $helper;
    protected $gallery_config;
    protected $pagination;
    protected $notification_helper;

    protected function setUp(): void
    {
        $this->gallery_log = $this->createMock(\phpbbgallery\core\log::class);
        $this->gallery_auth = $this->createMock(\phpbbgallery\core\auth\auth::class);
        $this->user = $this->createMock(\phpbb\user::class);
        $this->user->data = ['user_id' => 42];
        $this->language = $this->createMock(\phpbb\language\language::class);
        $this->db = $this->createMock(\phpbb\db\driver\driver_interface::class);
        $this->user_loader = $this->createMock(\phpbb\user_loader::class);
        $this->album = $this->createMock(\phpbbgallery\core\album\album::class);
        $this->template = $this->createMock(\phpbb\template\template::class);
        $this->helper = $this->createMock(\phpbb\controller\helper::class);
        $this->gallery_config = $this->createMock(\phpbbgallery\core\config::class);
        $this->pagination = $this->createMock(\phpbb\pagination::class);
        $this->notification_helper = $this->createMock(\phpbbgallery\core\notification\helper::class);

        $this->report = new report(
            $this->gallery_log,
            $this->gallery_auth,
            $this->user,
            $this->language,
            $this->db,
            $this->user_loader,
            $this->album,
            $this->template,
            $this->helper,
            $this->gallery_config,
            $this->pagination,
            $this->notification_helper,
            'phpbb_gallery_images',
            'phpbb_gallery_reports'
        );
    }

    public function test_add_report_inserts_and_logs()
    {
        $data = [
            'report_album_id' => 1,
            'report_image_id' => 2,
            'report_note' => 'Test note',
        ];

        $this->db->expects($this->at(0))
            ->method('sql_build_array')
            ->with('INSERT', $this->arrayHasKey('report_album_id'))
            ->willReturn('(sql_array)');

        $this->db->expects($this->at(1))
            ->method('sql_query')
            ->with($this->stringContains('INSERT INTO phpbb_gallery_reports'));

        $this->db->expects($this->at(2))
            ->method('sql_nextid')
            ->willReturn(123);

        $this->db->expects($this->at(3))
            ->method('sql_query')
            ->with($this->stringContains('UPDATE phpbb_gallery_images'));

        $this->gallery_log->expects($this->once())
            ->method('add_log')
            ->with(
                'moderator',
                'reportopen',
                1,
                2,
                $this->arrayHasKey(0)
            );

        $this->report->add($data);
    }

    public function test_add_report_missing_data_returns_early()
    {
        $this->db->expects($this->never())->method('sql_query');
        $this->gallery_log->expects($this->never())->method('add_log');
        $this->assertNull($this->report->add([]));
    }

    public function test_close_reports_by_image()
    {
        $this->db->expects($this->atLeastOnce())->method('sql_query');
        $this->db->expects($this->any())->method('sql_fetchrow')->willReturn(['image_album_id' => 1, 'image_id' => 2]);
        $this->db->expects($this->once())->method('sql_freeresult');
        $this->gallery_log->expects($this->any())->method('add_log');
        $this->report->close_reports_by_image([2], 99);
    }

    public function test_move_images()
    {
        $this->db->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains('UPDATE phpbb_gallery_reports'));
        $this->report->move_images([2, 3], 5);
    }

    public function test_move_album_content()
    {
        $this->db->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains('UPDATE phpbb_gallery_reports'));
        $this->report->move_album_content(1, 2);
    }

    public function test_delete()
    {
        $this->db->expects($this->atLeastOnce())->method('sql_query');
        $this->notification_helper->expects($this->once())->method('delete_notifications');
        $this->report->delete([1, 2]);
    }

    public function test_delete_images()
    {
        $this->db->expects($this->atLeastOnce())->method('sql_query');
        $this->db->expects($this->any())->method('sql_fetchrow')->willReturnOnConsecutiveCalls(['report_id' => 1], false);
        $this->db->expects($this->any())->method('sql_freeresult');
        $this->notification_helper->expects($this->once())->method('delete_notifications');
        $this->report->delete_images([2]);
    }

    public function test_delete_albums()
    {
        $this->db->expects($this->atLeastOnce())->method('sql_query');
        $this->db->expects($this->any())->method('sql_fetchrow')->willReturnOnConsecutiveCalls(['report_id' => 1], false);
        $this->db->expects($this->any())->method('sql_freeresult');
        $this->notification_helper->expects($this->once())->method('delete_notifications');
        $this->report->delete_albums([3]);
    }

    public function test_get_data_by_image_returns_array()
    {
        $this->db->expects($this->once())->method('sql_query');
        $this->db->expects($this->any())->method('sql_fetchrow')->willReturnOnConsecutiveCalls(
            [
                'report_id' => 1,
                'report_album_id' => 2,
                'reporter_id' => 3,
                'report_manager' => 4,
                'report_note' => 'note',
                'report_time' => 123456,
                'report_status' => 1,
            ],
            false
        );
        $this->db->expects($this->once())->method('sql_freeresult');
        $result = $this->report->get_data_by_image(2);
        $this->assertIsArray($result);
        $this->assertArrayHasKey(1, $result);
        $this->assertEquals(2, $result[1]['report_album_id']);
    }

    public function test_cast_mixed_int2array()
    {
        $this->assertEquals([1, 2], report::cast_mixed_int2array([1, 2]));
        $this->assertEquals([5], report::cast_mixed_int2array(5));
    }
}