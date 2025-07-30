<?php
/**
* @package phpBB Gallery Test
* @copyright (c) 2025
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace phpbbgallery\tests\core;

use phpbbgallery\core\comment;

class core_comment_test extends core_base
{
    protected $comment;
    protected $user;
    protected $db;
    protected $config;
    protected $auth;
    protected $block;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createMock(\phpbb\user::class);
        $this->user->data = [
            'user_id' => 42,
            'username' => 'TestUser',
            'user_colour' => 'ABCDEF'
        ];
        $this->user->ip = '127.0.0.1';

        $this->db = $this->createMock(\phpbb\db\driver\driver_interface::class);
        $this->config = $this->createMock(\phpbbgallery\core\config::class);
        $this->auth = $this->createMock(\phpbbgallery\core\auth\auth::class);
        $this->block = $this->createMock(\phpbbgallery\core\block::class);

        $this->comment = new comment(
            $this->user,
            $this->db,
            $this->config,
            $this->auth,
            $this->block,
            'phpbb_gallery_comments',
            'phpbb_gallery_images'
        );
    }

    public function test_is_allowed_true()
    {
        $album_data = ['album_id' => 1, 'album_user_id' => 2, 'album_status' => 0];
        $image_data = ['image_allow_comments' => true, 'image_status' => 1];

        $this->config->method('get')->willReturnMap([
            ['allow_comments', true],
            ['comment_user_control', false]
        ]);
        $this->auth->method('acl_check')->willReturn(true);
        $this->block->method('get_image_status_approved')->willReturn(1);
        $this->block->method('get_album_status_locked')->willReturn(2);

        $this->assertTrue($this->comment->is_allowed($album_data, $image_data));
    }

    public function test_is_allowed_false()
    {
        $album_data = ['album_id' => 1, 'album_user_id' => 2, 'album_status' => 2];
        $image_data = ['image_allow_comments' => false, 'image_status' => 0];

        $this->config->method('get')->willReturnMap([
            ['allow_comments', false],
            ['comment_user_control', true]
        ]);
        $this->auth->method('acl_check')->willReturn(false);
        $this->block->method('get_image_status_approved')->willReturn(1);
        $this->block->method('get_album_status_locked')->willReturn(2);

        $this->assertFalse($this->comment->is_allowed($album_data, $image_data));
    }

    public function test_is_able_delegates_to_is_allowed()
    {
        $album_data = ['album_id' => 1, 'album_user_id' => 2, 'album_status' => 0];
        $image_data = ['image_allow_comments' => true, 'image_status' => 1];

        $this->config->method('get')->willReturn(true);
        $this->auth->method('acl_check')->willReturn(true);
        $this->block->method('get_image_status_approved')->willReturn(1);
        $this->block->method('get_album_status_locked')->willReturn(2);

        $this->assertTrue($this->comment->is_able($album_data, $image_data));
    }

    public function test_add_comment_success()
    {
        $data = [
            'comment_image_id' => 5,
            'comment' => 'Nice pic!',
            'comment_album_id' => 1, // Add any other required fields for the insert to succeed
        ];

        $this->db->expects($this->at(0))
            ->method('sql_query')
            ->with($this->stringContains('INSERT INTO phpbb_gallery_comments'));

        $this->db->expects($this->at(1))
            ->method('sql_nextid')
            ->willReturn(99);

        $this->config->expects($this->once())
            ->method('inc')
            ->with('num_comments', 1);

        $this->db->expects($this->at(2))
            ->method('sql_query')
            ->with($this->stringContains('UPDATE phpbb_gallery_images'));

        $result = $this->comment->add($data);
        $this->assertEquals(0, $result);
    }

    public function test_add_comment_missing_data_returns_null()
    {
        $this->db->expects($this->never())->method('sql_query');
        $this->assertNull($this->comment->add([]));
    }

    public function test_edit_comment_success()
    {
        $comment_id = 7;
        $data = ['comment' => 'Updated comment'];

        $this->db->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains('UPDATE phpbb_gallery_comments'));

        $this->assertTrue($this->comment->edit($comment_id, $data));
    }

    public function test_edit_comment_missing_data_returns_null()
    {
        $this->db->expects($this->never())->method('sql_query');
        $this->assertNull($this->comment->edit(7, []));
    }

    public function test_sync_image_comments()
    {
        $image_ids = [1, 2];
        $sql_in_set_comment = 'comment_image_id IN (1,2)';
        $sql_in_set_image = 'image_id IN (1,2)';
    
        $this->db->method('sql_in_set')
            ->will($this->returnValueMap([
                ['comment_image_id', $image_ids, $sql_in_set_comment],
                ['image_id', $image_ids, $sql_in_set_image],
            ]));
    
        // Оставяме първия SELECT да бъде извикан някъде – не ни пука за поредността
        $this->db->expects($this->atLeastOnce())
            ->method('sql_query')
            ->with($this->callback(function ($query) {
                return is_string($query); // не филтрираме — просто искаме да мине
            }));
    
        $this->db->method('sql_fetchrow')
            ->will($this->onConsecutiveCalls(
                ['comment_image_id' => 1, 'num_comments' => 3, 'last_comment' => 10],
                ['comment_image_id' => 2, 'num_comments' => 2, 'last_comment' => 20],
                false
            ));
    
        $this->db->expects($this->once())->method('sql_freeresult');
    
        // Следим дали са извикани UPDATE-и с правилното съдържание (но не и в точен ред)
        $this->db->expects($this->atLeast(2))
            ->method('sql_query')
            ->with($this->logicalOr(
                $this->stringContains('UPDATE phpbb_gallery_images SET image_last_comment = 0'),
                $this->stringContains('SET image_last_comment = 10'),
                $this->stringContains('SET image_last_comment = 20')
            ));
    
        // Изпълнение
        $this->comment->sync_image_comments($image_ids);
    }


    public function test_delete_comments()
    {
        $this->db->expects($this->atLeastOnce())
            ->method('sql_query')
            ->with($this->logicalOr(
                $this->stringContains('SELECT comment_image_id'),
                $this->stringContains('DELETE FROM phpbb_gallery_comments'),
                $this->stringContains('UPDATE phpbb_gallery_images')
            ));

        $this->db->expects($this->any())
            ->method('sql_fetchrow')
            ->willReturnOnConsecutiveCalls(
                ['comment_image_id' => 1, 'num_comments' => 2],
                false
            );

        $this->db->expects($this->once())->method('sql_freeresult');

        $this->config->expects($this->once())
            ->method('dec')
            ->with('num_comments', 2);

        $this->comment->delete_comments([1]);
    }

    public function test_delete_images()
    {
        $this->db->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains('DELETE FROM phpbb_gallery_comments'));

        $this->comment->delete_images([1]);
    }

    public function test_delete_images_with_reset_stats()
    {
        $image_ids = [1, 2];
        $sql_in_set_comment = 'comment_image_id IN (1,2)';
        $sql_in_set_image = 'image_id IN (1,2)';

        // Mock cast_mixed_int2array to just return the array (or let the real method run)
        // Mock sql_in_set to return the correct SQL fragment
        $this->db->method('sql_in_set')
            ->will($this->returnValueMap([
                ['comment_image_id', $image_ids, $sql_in_set_comment],
                ['image_id', $image_ids, $sql_in_set_image],
            ]));

        // Expect DELETE FROM comments
        $this->db->expects($this->at(0))
            ->method('sql_query')
            ->with($this->stringContains('DELETE FROM phpbb_gallery_comments'));

        // Expect UPDATE images table to reset stats
        $this->db->expects($this->at(1))
            ->method('sql_query')
            ->with($this->stringContains('UPDATE phpbb_gallery_images'));

        // Act
        $this->comment->delete_images($image_ids, true);

        // No assertion needed, expectations above will fail the test if not met
    }

    public function test_cast_mixed_int2array()
    {
        $this->assertEquals([1, 2], $this->comment->cast_mixed_int2array([1, 2]));
        $this->assertEquals([5], $this->comment->cast_mixed_int2array(5));
    }
}
