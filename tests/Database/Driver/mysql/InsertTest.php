<?php
namespace Wei\Base\Tests\Database\Driver\mysql;

use Wei\Base\Database\Driver\mysql\Insert;
use Wei\Base\Database\Query\ConnectionFactor;
use Wei\Base\Tests\WeiTestCase;

class InsertTest extends WeiTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass(); // TODO: Change the autogenerated stub
        //清除数据
        ConnectionFactor::getInstance()->query("delete from test where `name`='20170516--1416' or `name` like '20170516--1436%'");
    }

    /**
     * 测试单行数据
     */
    public function testInsertRow()
    {
        $obj = new Insert(ConnectionFactor::getInstance(), 'test');
        ConnectionFactor::enabledSqlLog();
        $values = [
            'name' => '20170516--1416',
            'age' => 1601,
            'uid' => 1602,
            'created' => '2017-05-16 14:16',
        ];
        $result = $obj->insert($values);
        $this->assertEquals('1', $result);
    }

    /**
     * 测试插入多行数据
     */
    public function testInsertRows()
    {
        $obj = new Insert(ConnectionFactor::getInstance(), 'test');
        ConnectionFactor::enabledSqlLog();
        $rowsValue[] = [
            'name' => '20170516--143601',
            'age' => 1603,
            'uid' => 1604,
            'created' => '2017-05-16 14:36',
        ];
        $rowsValue[] = [
            'name' => '20170516--143602',
            'age' => 1605,
            'uid' => 1606,
            'created' => '2017-05-16 14:36',
        ];
        $rowsValue[] = [
            'name' => '20170516--143603',
            'age' => 1607,
            'uid' => 1608,
            'created' => '2017-05-16 14:36',
        ];

        $result = $obj->insertAll($rowsValue);
        $this->assertEquals('3', $result);

        $rowsValue = null;
        $rowsValue[] = [
            'name' => '20170516--143604',
            'age' => 1609,
            'uid' => 1610,
            'created' => '2017-05-16 14:36',
        ];
        $rowsValue[] = [
            'name' => '20170516--143605',
            'age' => 1611,
            'uid' => 1612,
            'created' => '2017-05-16 14:36',
        ];
        $result = $obj->insertAll($rowsValue);
        $this->assertEquals('2', $result);
    }
}