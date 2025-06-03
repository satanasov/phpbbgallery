<?php

/**
*
* PhpBB Gallery extension for the phpBB Forum Software package.
*
* @copyright (c) 2025 Your Name
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbbgallery\tests\core;
/**
* @group core
*/
require_once dirname(__FILE__) . '/../../../../includes/functions.php';

class core_url_test extends core_base
{
    protected $gallery_url;

    public function setUp() : void
    {
        parent::setUp();

        // Mock dependencies
        $this->template = $this->getMockBuilder(\phpbb\template\template::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(\phpbb\request\request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = [
            'server_name' => 'localhost',
            'force_server_vars' => 0,
            'server_protocol' => 'http://',
        ];

        $this->configObj = $this->getMockBuilder(\phpbb\config\config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configObj->method('offsetGet')->willReturnCallback(function($key) {
            $defaults = [
                'server_name' => 'localhost',
                'force_server_vars' => 0,
                'server_protocol' => 'http://',
            ];
            return $defaults[$key] ?? null;
        });
        $this->configObj->method('offsetExists')->willReturn(true);

        $this->phpbb_root_path = '/';
        $this->php_ext = 'php';

        $this->gallery_url = new \phpbbgallery\core\url(
            $this->template,
            $this->request,
            $this->configObj,
            $this->phpbb_root_path,
            $this->php_ext
        );
    }

    public function data_build_url()
    {
        return array(
            'album' => array(
                'album',
                array('album_id' => 5),
                '/gallery/album/5'
            ),
            'image' => array(
                'image',
                array('image_id' => 42),
                '/gallery/image/42'
            ),
            // Add more cases as needed
        );
    }

    /**
    * @dataProvider data_build_url
    */
    public function test_build_url($type, $params, $expected)
    {
        $result = $this->gallery_url->build_url($type, $params);
        $this->assertEquals($expected, $result);
    }

    public function data_parse_url()
    {
        return array(
            'album' => array(
                '/gallery/album/5',
                array('type' => 'album', 'album_id' => 5)
            ),
            'image' => array(
                '/gallery/image/42',
                array('type' => 'image', 'image_id' => 42)
            ),
            // Add more cases as needed
        );
    }

    /**
    * @dataProvider data_parse_url
    */
    public function test_parse_url($url, $expected)
    {
        $result = $this->gallery_url->parse_url($url);
        $this->assertEquals($expected, $result);
    }

    public function test_append_sid()
    {
        $url = '/gallery/album/5';
        $sid = '123abc';
        $this->user->session_id = $sid;
        $result = $this->gallery_url->append_sid($url);
        $this->assertStringContainsString('sid=123abc', $result);
    }

    public function test_get_root_path()
    {
        $this->assertEquals($this->root_path, $this->gallery_url->get_root_path());
    }

    // Add more tests for other public methods in core\url.php as needed
}