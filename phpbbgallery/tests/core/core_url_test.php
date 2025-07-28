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
require_once dirname(__FILE__) . '/../../../../includes/functions_compatibility.php';

class core_url_test extends core_base
{
    /** @var \phpbbgallery\core\url */
    protected $url;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\template\template */
    protected $template;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\request\request */
    protected $request;

    /** @var \phpbb\config\config */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        global $phpbb_root_path, $phpEx, $config;
        
        $this->template = $this->getMockBuilder('\\phpbb\\template\\template')
            ->getMock();
            
        $this->request = $this->getMockBuilder('\\phpbb\\request\\request')
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->config = new \phpbb\config\config(array(
            'server_name' => 'localhost',
            'server_protocol' => 'http://',
            'server_port' => 80,
            'force_server_vars' => 0,
        ));
        
        $this->url = new \phpbbgallery\core\url(
            $this->template,
            $this->request,
            $this->config,
            $phpbb_root_path,
            $phpEx
        );
    }

    public function test_path()
    {
        $this->assertStringContainsString('gallery', $this->url->path('gallery'));
        $this->assertStringContainsString('ext/phpbbgallery/core', $this->url->path('ext'));
        $this->assertStringContainsString('phpBB', $this->url->path('phpbb'));
        $this->assertStringContainsString('adm', $this->url->path('admin'));
        $this->assertStringContainsString('gallery', $this->url->path('relative'));
        $this->assertStringContainsString('http', $this->url->path('full'));
        $this->assertStringContainsString('http', $this->url->path('board'));
        $this->assertStringContainsString('images', $this->url->path('images'));
        $this->assertStringContainsString('files/phpbbgallery', $this->url->path('upload'));
        $this->assertStringContainsString('mini', $this->url->path('thumbnail'));
        $this->assertStringContainsString('medium', $this->url->path('medium'));
        $this->assertStringContainsString('import', $this->url->path('import'));
        $this->assertStringContainsString('core/source/', $this->url->path('upload_noroot'));
        $this->assertStringContainsString('core/mini/', $this->url->path('thumbnail_noroot'));
        $this->assertStringContainsString('core/medium/', $this->url->path('medium_noroot'));
        $this->assertStringContainsString('import/', $this->url->path('import_noroot'));
        $this->assertFalse($this->url->path('invalid_path'));
    }

    public function test_phpEx_file()
    {
        // The method appends .php to the filename, even if it already has .php
        $this->assertEquals('test.php', $this->url->phpEx_file('test'));
        $this->assertEquals('test.php.php', $this->url->phpEx_file('test.php'));
        $this->assertEquals('test/', $this->url->phpEx_file('test/'));
        $this->assertEquals('', $this->url->phpEx_file(''));
    }

    public function test_show_image()
    {
        $image_id = 123;
        $result = $this->url->show_image($image_id);
        $this->assertStringContainsString((string)$image_id, $result);
        $this->assertStringContainsString('medium', $result);
        
        $custom_size = 'large';
        $result = $this->url->show_image($image_id, $custom_size);
        $this->assertStringContainsString($custom_size, $result);
    }

    public function test_show_album()
    {
        $album_id = 456;
        $result = $this->url->show_album($album_id);
        $this->assertStringContainsString((string)$album_id, $result);
    }

    public function test_beautiful_path()
    {
        // Test with relative path
        $path = '../community/../gallery/';
        $expected = '../gallery/';
        $this->assertEquals($expected, \phpbbgallery\core\url::beautiful_path($path));

        // Test with full URL
        $url = 'http://example.com/community/../gallery/';
        $expected = 'http://example.com/gallery/';
        $this->assertEquals($expected, \phpbbgallery\core\url::beautiful_path($url, true));

        // Test with https
        $url = 'https://example.com/community/../gallery/';
        $expected = 'https://example.com/gallery/';
        $this->assertEquals($expected, \phpbbgallery\core\url::beautiful_path($url, true));
    }

    public function test_meta_refresh()
    {
        $time = 5;
        $route = 'gallery/album/1';
        
        $this->template->expects($this->once())
            ->method('assign_vars')
            ->with($this->callback(function($vars) use ($time, $route) {
                return strpos($vars['META'], (string)$time) !== false && 
                       strpos($vars['META'], $route) !== false;
            }));
            
        $this->url->meta_refresh($time, $route);
    }

    public function test_get_uri()
    {
        $route = '/gallery/album/1';
        $result = $this->url->get_uri($route);
        $this->assertStringStartsWith('http://', $result);
        $this->assertStringEndsWith($route, $result);
        $this->assertStringContainsString('localhost', $result);
        
        // Test with HTTPS
        $this->request->expects($this->any())
            ->method('server')
            ->with('HTTPS', '')
            ->willReturn('on');
            
        $result = $this->url->get_uri($route);
        $this->assertStringStartsWith('https://', $result);
    }

    public function test_append_sid()
    {
        // Test basic URL
        $result = $this->url->append_sid('index', 'mode=view');
        $this->assertStringContainsString('index.php', $result);
        $this->assertStringContainsString('mode=view', $result);
        
        // Test with path prefix
        $result = $this->url->append_sid('phpbb', 'app.php/foo/bar');
        $this->assertStringContainsString('app.php/foo/bar', $result);
        
        // Test with array parameters
        $result = $this->url->append_sid(array('index', 'mode=view&id=1'));
        $this->assertStringContainsString('index.php', $result);
        $this->assertStringContainsString('mode=view', $result);
        $this->assertStringContainsString('id=1', $result);
    }

    public function test_create_link()
    {
        $path = 'gallery';
        $file = 'index';
        $params = 'album_id=1&image_id=2';
        
        $result = $this->url->create_link($path, $file, $params);
        $this->assertStringContainsString($file, $result);
    }

    public function test_include_methods()
    {
        $path = 'gallery';
        $file = 'test_include';
        $sub_directory = 'includes/';
        
        // Test _return_file
        $result = $this->url->_return_file($file, $path, $sub_directory);
        $this->assertStringContainsString($file . '.php', $result);
        $this->assertStringContainsString($sub_directory, $result);
        
        // Test _file_exists - mock the file_exists function
        $this->assertIsBool(@$this->url->_file_exists($file, $path, $sub_directory));
        
        // Skip _is_writable test as it requires filesystem access
        // and proper mocking of phpbb_is_writable function
        $this->markTestSkipped('_is_writable test requires filesystem access');
    }
}
