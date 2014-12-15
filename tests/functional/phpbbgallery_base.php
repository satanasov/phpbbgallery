<?php
/**
*
* Gallery Tests
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
namespace phpbbgallery\tests\functional;
/**
* @group functional
*/
class phpbbgallery_base extends \phpbb_functional_test_case
{
	private $path;
	static protected function setup_extensions()
	{
		return array('phpbbgallery/core', 'phpbbgallery/exif', 'phpbbgallery/acpimport');
	}
	public function setUp()
	{
		parent::setUp();
		$this->path = __DIR__ . '/images/';
		
	}	
	
	private function upload_file($filename, $mimetype, $upload_path)
	{
		$file = array(
			'tmp_name' => $this->path . $filename,
			'name' => $filename,
			'type' => $mimetype,
			'size' => filesize($this->path . $filename),
			'error' => UPLOAD_ERR_OK,
		);
		
		$crawler = self::$client->request(
			'POST',
			$upload_path . $this->sid,
			array('mode' => 'upload'),
			array('image_file_0' => $file)
		);
		return $file;
	}
}