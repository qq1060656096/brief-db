<?php
namespace Wei\BriefDB\Tests;

use Wei\BriefDB\Database\Db;

/**
 * 数据库测试
 *
 * Class DbTest
 * @package Wei\BriefDB\Tests
 */
class DbTest extends WeiTestCase
{
    /**
     *
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass(); // TODO: Change the autogenerated stub
        // 删除测试数据
        $data = [
            'name' => '20170816.2347',
            'age' => 17081801,
            'uid' => 17081802,
            'created' => '2017-08-16 23:47',
        ];
        Db::getInsert()->from('test')->insert($data);
        $data = [
            'name' => '20170816.2352',
            'age' => 17081801,
            'uid' => 17081802,
            'created' => '2017-08-16 23:52',
        ];
        Db::getInsert()->from('test')->insert($data);


        // 更改测试数据
        Db::getDelete()->from('test')
            ->condition('name', '20170816.2357.update')
            ->delete();
        $data = [
            'name' => '20170816.2357.update',
            'age' => 17081801,
            'uid' => 17081802,
            'created' => '2017-08-16 23:57',
        ];
        Db::getInsert()->from('test')->insert($data);


        // 查询测试数据
        Db::getDelete()->from('test')
            ->condition('name', '20170816.2359.select%', 'like')
            ->delete();
        $data = [
            'name' => '20170816.2359.select1',
            'age' => 17081801,
            'uid' => 17081802,
            'created' => '2017-08-16 23:59',
        ];
        Db::getInsert()->from('test')->insert($data);

        $data = [
            'name' => '20170816.2359.select2',
            'age' => 17081801,
            'uid' => 17081802,
            'created' => '2017-08-16 23:59',
        ];
        Db::getInsert()->from('test')->insert($data);
    }

    /**
     * 测试删除
     */
    public function testDelete()
    {
        $count = Db::getDelete()->from('test')
            ->condition('name', ['20170816.2347', '20170816.2352'])
            ->delete();
        $this->assertEquals('2', $count);
    }

    /**
     * 测试更改
     */
    public function testUpdate()
    {
        $data = [
            'age' => 170818011,
            'uid' => 170818022,
        ];
        $count = Db::getUpdate()->from('test')
            ->condition('name', '20170816.2357.update')
            ->update($data);
        $this->assertEquals('1', $count);
    }

    /**
     * 测试查询
     */
    public function testSelete()
    {
        $count = Db::getSelect()->from('test')
            ->condition('name', '20170816.2359.se%', 'like')
            ->findCount();
        $this->assertEquals('2', $count);

        $result = Db::getSelect()->from('test')
            ->condition('name', '20170816.2359.se%', 'like')
            ->orderBy('name', 'asc')
            ->findAll();

        $this->assertEquals('20170816.2359.select1', $result[0]['name']);
    }
}