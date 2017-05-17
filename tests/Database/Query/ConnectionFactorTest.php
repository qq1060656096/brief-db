<?php
namespace Wei\Base\Tests\Database\Query;

use Wei\Base\Database\Query\ConnectionFactor;
use Wei\Base\Tests\WeiTestCase;

/**
 * 测试连接工厂类
 *
 * Class ConnectionFactorTest
 * @package Wei\Base\Tests\Database\Query
 */
class ConnectionFactorTest extends WeiTestCase
{
    /**
     * 测试
     */
    public function test()
    {
        ConnectionFactor::getInstance('sqlite');
        print_r(ConnectionFactor::getCurrentConnectionName());
        $class = new \ReflectionClass(ConnectionFactor::class);
        $arr = $class->getStaticProperties();
        print_r($arr);
    }
}