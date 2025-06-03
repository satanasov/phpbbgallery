<?php

namespace phpbbgallery\tests\core;
/**
* @group core
*/
require_once dirname(__FILE__) . '/../../../../includes/functions.php';

if (!function_exists('append_sid')) {
    function append_sid($url, $params = '', $is_amp = true, $session_id = false) {
        // For testing, just return the url and params concatenated
        if ($params !== '') {
            return $url . '?' . $params;
        }
        return $url;
    }
}
if (!function_exists('redirect')) {
    function redirect($url) {
        throw new \Exception('redirect called: ' . $url);
    }
}

class core_url_test extends core_base
{
    protected $gallery_url;

    public function setUp() : void
    {
        parent::setUp();

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
            ['medium_noroot', 'files/phpbbgallery/core/medium/'],
            ['import_noroot', 'files/phpbbgallery/import/'],
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

    public function test_append_sid()
    {
        $result = $this->gallery_url->append_sid('gallery', 'index');
        $this->assertStringContainsString('gallery', $result);
        $this->assertStringContainsString('index', $result);
    }

    public function test_show_image()
    {
        $expected = $this->gallery_url->path('full') . 'image/123/medium';
        $result = $this->gallery_url->show_image(123, 'medium');
        $this->assertEquals($expected, $result);
    }

    public function test_show_album()
    {
        $expected = $this->gallery_url->path('full') . 'album/42';
        $result = $this->gallery_url->show_album(42);
        $this->assertEquals($expected, $result);
    }

    public function test_create_link()
    {
        $result = $this->gallery_url->create_link('gallery', 'index', 'foo=bar&amp;baz=qux');
        $this->assertStringContainsString('gallery', $result);
        $this->assertStringContainsString('index', $result);
        $this->assertStringContainsString('foo=bar&baz=qux', $result);
        $this->assertStringNotContainsString('&amp;', $result);
    }

    public function test_redirect()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('redirect called: /gallery/index.php');
        $this->gallery_url->redirect('gallery', 'index');
    }
}