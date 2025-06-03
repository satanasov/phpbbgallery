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

    public function data_path()
    {
        return [
            ['gallery', '/gallery/'],
            ['ext', '/ext/phpbbgallery/core/'],
            ['phpbb', '/'],
            ['admin', '/adm/'],
            ['relative', 'gallery/'],
            ['images', '/ext/phpbbgallery/core/images/'],
            ['upload', '/files/phpbbgallery/core/source/'],
            ['thumbnail', '/files/phpbbgallery/core/mini/'],
            ['medium', '/files/phpbbgallery/core/medium/'],
            ['import', '/files/phpbbgallery/import/'],
            ['upload_noroot', 'files/phpbbgallery/core/source/'],
            ['thumbnail_noroot', 'files/phpbbgallery/core/mini/'],
        ];
    }

    /**
     * @dataProvider data_path
     */
    public function test_path($type, $expected)
    {
        $result = $this->gallery_url->path($type);
        $this->assertEquals($expected, $result);
    }

    // You may need to adjust this test depending on how append_sid is implemented
    public function test_append_sid()
    {
        // This is a placeholder. You may need to adjust based on actual implementation.
        $url = '/gallery/album/5';
        $result = $this->gallery_url->append_sid($url);
        $this->assertIsString($result);
    }
}