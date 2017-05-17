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
     * 测试数据库多连接
     */
    public function testMultiGetInstance()
    {
        //获取 mysql数据库连接
        ConnectionFactor::getInstance('default');

        //获取 sqlite数据库连接
        ConnectionFactor::getInstance('sqlite');

        $class  = new \ReflectionClass(ConnectionFactor::class);
        $arr    = $class->getStaticProperties();
        $this->assertEquals('sqlite', $arr['currentConnectionName']);
        $this->assertEquals(2, count($arr['connectionDataInstance']));
//        print_r($arr['connectionDataInstance']);
//        print_r($arr['currentConnectionName']);
    }
}