<?php
/**
*
* PhpBB Gallery extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Lucifer <https://www.anavaro.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbbgallery\tests\core;
/**
 * @group core
 */
require_once dirname(__FILE__) . '/../../../../includes/functions.php';

class core_block_test extends core_base
{
	public function setUp()
	{
		parent::setUp();

		$this->gallery_block = new \phpbbgallery\core\block();
	}

	public function test_get_album_status_locked()
	{
		$this->assertEquals($this->gallery_block->get_album_status_locked(), \phpbbgallery\core\block::ALBUM_LOCKED);
	}
	public function test_get_album_public()
	{
		$this->assertEquals($this->gallery_block->get_album_public(), \phpbbgallery\core\block::PUBLIC_ALBUM);
	}
	public function test_get_album_type_upload()
	{
		$this->assertEquals($this->gallery_block->get_album_type_upload(), \phpbbgallery\core\block::TYPE_UPLOAD);
	}

	public function test_get_image_status_unapproved()
	{
		$this->assertEquals($this->gallery_block->get_image_status_unapproved(), \phpbbgallery\core\block::STATUS_UNAPPROVED);
	}
	public function test_get_image_status_approved()
	{
		$this->assertEquals($this->gallery_block->get_image_status_approved(), \phpbbgallery\core\block::STATUS_APPROVED);
	}
	public function test_get_image_status_locked()
	{
		$this->assertEquals($this->gallery_block->get_image_status_locked(), \phpbbgallery\core\block::STATUS_LOCKED);
	}
	public function test_get_image_status_orphan()
	{
		$this->assertEquals($this->gallery_block->get_image_status_orphan(), \phpbbgallery\core\block::STATUS_ORPHAN);
	}
	public function test_get_no_contest()
	{
		$this->assertEquals($this->gallery_block->get_no_contest(), \phpbbgallery\core\block::NO_CONTEST);
	}
	public function test_get_in_contest()
	{
		$this->assertEquals($this->gallery_block->get_in_contest(), \phpbbgallery\core\block::IN_CONTEST);
	}
}