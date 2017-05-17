<?php
namespace Wei\Base\Tests\Database\Driver\mysql;


use Wei\Base\Database\Query\ConnectionFactor;
use Wei\Base\Database\Driver\mysql\Delete;
use Wei\Base\Tests\WeiTestCase;

class DeleteTest extends WeiTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass(); // TODO: Change the autogenerated stub

        ConnectionFactor::getInstance()->delete('test', ['name' => '20170516--1544']);
        ConnectionFactor::getInstance()->delete('test', ['name' => '20170516--154601']);
        ConnectionFactor::getInstance()->delete('test', ['name' => '20170516--154602']);
        $values = [
            'name' => '20170516--1544',
            'age' => 1601,
            'uid' => 1602,
            'created' => '2017-05-16 15:44',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);

        $values = [
            'name' => '20170516--154601',
            'age' => 1601,
            'uid' => 1602,
            'created' => '2017-05-16 15:46',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);

        $values = [
            'name' => '20170516--154602',
            'age' => 1601,
            'uid' => 1602,
            'created' => '2017-05-16 15:46',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);
    }

    /**
     * 测试删除
     */
    public function test()
    {
        $obj = new Delete(ConnectionFactor::getInstance(), 'test');
        $obj->condition('name', '20170516--1544');
        $result = $obj->delete();
        $this->assertEquals('1', $result);

//        ConnectionFactor::enabledSqlLog();
        $obj = new Delete(ConnectionFactor::getInstance(), 'test');
        $obj->condition('name', '20170516--1546%', 'like');
        $result = $obj->delete();
        $this->assertEquals('2', $result);
    }
}