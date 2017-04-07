<?php
namespace Wei\Base\Tests\LimitFrequency;

use Wei\Base\Exception\LimitFrequencyException;
use Wei\Base\LimitFrequency\LimitFrequency;
use Wei\Base\Tests\WeiTestCase;

/**
 * 限制访问频率单元测试
 *
 * Class LimitFrequencyTest
 * @package Wei\Base\Tests\LimitFrequency
 */
class LimitFrequencyTest extends WeiTestCase
{
    public static function setUpBeforeClass()
    {
        $cache_dir      = rtrim(__dir__, '/').'/run-cache';
        $cache          = new \Doctrine\Common\Cache\FilesystemCache($cache_dir);
//        $cache->delete('1060656096@qq.com');
    }

    /**
     * 测试键
     */
    public function testBuildKey()
    {
        $limitFrequency = new LimitFrequency();
        $key = $limitFrequency->buildKey('123', 'start__', '__end');
        $this->assertEquals("start__".md5(json_encode('123'))."__end", $key);

        $key = $limitFrequency->buildKey([123,456], 'start__', '__end');
        $this->assertEquals("start__".md5(json_encode([123,456]))."__end", $key);
    }

    /**
     * 测试访问频率
     *
     * @expectedException \Wei\Base\Exception\LimitFrequencyException
     * @expectedExceptionCode 41204
     */
    public function testThrowCacheNotNull()
    {
        $limitFrequency = new LimitFrequency();
        $limitFrequency->getCache();
    }

    public function testCache()
    {
        $cache_dir      = rtrim(__dir__, '/').'/run-cache';
        $cache          = new \Doctrine\Common\Cache\FilesystemCache($cache_dir);
        $value = $cache->fetch('doctrine2-cache');
        if ($value == false) {
            $value = 1;
            $cache->save('doctrine2-cache', $value, 2);
        }else{
            $value ++;
            $cache->save('doctrine2-cache', $value, 2);
        }
    }

    public function test2()
    {
//        $cache_dir      = rtrim(__dir__, '/').'/run-cache';
//        $cache          = new \Doctrine\Common\Cache\FilesystemCache($cache_dir);
//        $limitFrequency = new LimitFrequency();
//        $limitFrequency->setCache($cache);
//        //1060656096@qq.com在2秒类访问频率
//        $num = $limitFrequency->getFrequency('1060656096@qq.com', 60);
//        var_dump($num);
//        sleep(10);
//        $num = $limitFrequency->getFrequency('1060656096@qq.com', 60);
//        $num = $limitFrequency->getFrequency('1060656096@qq.com', 260);
//        var_dump($num);
    }
}