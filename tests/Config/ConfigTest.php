<?php
namespace Zwei\BriefDB\Tests\Config;


use Zwei\BriefDB\Common\Composer;
use Zwei\BriefDB\Config\Config;
use Zwei\BriefDB\Exception\ConfigException;
use Zwei\BriefDB\Tests\WeiTestCase;

/**
 * 读取配置单元测试
 *
 */
class ConfigTest extends WeiTestCase
{
    /**
     * 测试获取目录
     */
    public function testGetDirPath()
    {
        $dir = str_replace('\\', '/', Composer::getComposerVendorDir()).'/config';
        $this->assertEquals($dir, Config::getDirPath());

        Config::setDir('/etc/bin');
        $this->assertEquals('/etc/bin', Config::getDirPath());
    }

    /**
     * 读取配置信息
     */
    public function testGet()
    {
        Config::setDir(null);

        $this->assertEquals('test value', Config::get('test'));
        $this->assertEquals('test2 value', Config::get('test2', 'config2.php'));
        $this->assertEquals('test value config2.php', Config::get('test', 'config2.php'));
        $config = Config::get(null,null, false, true);
//        print_r($config);
    }

    /**
     * 测试配置项找不到
     *
     * @expectedException \Zwei\BriefDB\Exception\ConfigException
     * @expectedExceptionCode 41301
     */
    public function testThrowKeyNotFound()
    {
         Config::get('test20170407--1350');
    }
    /**
     * 测试配置文件找不到
     * @expectedException \Zwei\BriefDB\Exception\ConfigException
     * @expectedExceptionCode 41302
     */
    public function testThrowFileNotFound()
    {
         Config::get('test20170407--1351', 'config20170407--1351');
    }
}