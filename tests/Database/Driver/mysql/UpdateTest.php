<?php
namespace Wei\BriefDB\Tests\Database\Driver\mysql;

use Wei\BriefDB\Database\Query\BatchUpdate;
use Wei\BriefDB\Database\Query\Condition;
use Wei\BriefDB\Database\Query\ConnectionFactor;
use Wei\BriefDB\Database\Driver\mysql\Update;
use Wei\BriefDB\Tests\WeiTestCase;

class UpdateTest extends WeiTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass(); // TODO: Change the autogenerated stub

        ConnectionFactor::getInstance()->delete('test', ['name' => '20170516--1737']);
        ConnectionFactor::getInstance()->delete('test', ['name' => '20170516--173701']);
        ConnectionFactor::getInstance()->delete('test', ['name' => '20170516--173702']);
        ConnectionFactor::getInstance()->delete('test', ['name' => '20170517--0938']);
        ConnectionFactor::getInstance()->delete('test', ['name' => '20170517--093801']);
        ConnectionFactor::getInstance()->delete('test', ['name' => '20170531.0848']);
        $values = [
            'name' => '20170516--1737',
            'age' => 1737,
            'uid' => 17370,
            'created' => '2017-05-16 17:37',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);

        $values = [
            'name' => '20170516--173701',
            'age' => 173701,
            'uid' => 1737011,
            'created' => '2017-05-16 17:37',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);

        $values = [
            'name' => '20170516--173702',
            'age' => 173702,
            'uid' => 1737022,
            'created' => '2017-05-16 17:37',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);


        $values = [
            'name' => '20170517--0938',
            'age' => 1709381,
            'uid' => 1709382,
            'created' => '2017-05-17 09:38',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);

        $values = [
            'name' => '20170517--093801',
            'age' => 1709383,
            'uid' => 1709384,
            'created' => '2017-05-17 09:38',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);

        $values = [
            'name' => '20170531.0848',
            'age' => 3108481,
            'uid' => 3108482,
            'created' => '2017-05-31 08:48',
        ];
        ConnectionFactor::getInstance()->insert('test', $values);
    }

    /**
     * 更新
     */
    public function testUpdate()
    {
        $obj = new Update(ConnectionFactor::getInstance(), 'test');
        $obj->condition('name', '20170516--1737');
        $data = [
            'name' => '20170516--1737',
            'age' => 173710,
            'uid' => 17370010,
            'created' => '2017-05-16 17:37:01',
        ];
        ConnectionFactor::enabledSqlLog();
        $result = $obj->update($data);
        $this->assertEquals('1', $result);

    }

    /**
     * 更新
     */
    public function testUpdateSetRaw()
    {
        $obj = new Update(ConnectionFactor::getInstance(), 'test');
        $obj->condition('name', '20170531.0848');
        $data = [
            'name' => '20170531.0848',
            'age' => 173710,
            'uid' => [
                'raw' => "? + ? - ?",
                1,
                2,
                3
            ],
            'created' => '2017-05-31 08:48:01',
        ];
        ConnectionFactor::enabledSqlLog();
        $result = $obj->update($data);
        print_r(ConnectionFactor::getLastRawSql());
        $this->assertEquals('1', $result);

    }



    /**
     * 批量更新
     */
    public function testUpdateAll()
    {
        ConnectionFactor::enabledSqlLog();
        $bathUpdate = new BatchUpdate();
        $condition = new Condition('AND');
        $condition->condition('name', '20170516--173702');
        $data = [
            'name' => '20170516--173702',
            'age' => 17370210,
            'uid' => 173702210,
            'created' => '2017-05-16 17:37:01',
        ];
        $bathUpdate->addData($condition, $data);

        $condition = new Condition('AND');
        $condition->condition('name', '20170516--173701');
        $data = [
            'name' => '20170516--173701',
            'age' => 173701,
            'uid' => 1737011,
            'created' => '2017-05-16 17:37',
        ];
        $bathUpdate->addData($condition, $data);
        $obj = new Update(ConnectionFactor::getInstance(), 'test');
        $result = $obj->updateAll($bathUpdate);
        $this->assertEquals('1', $result);
        //严格模式
        $obj    = new Update(ConnectionFactor::getInstance(), 'test');
        $result = $obj->updateAll($bathUpdate, true);
        $this->assertEquals(false, $result);
    }

    /**
     * 批量更新严格模式
     */
    public function testUpdateAllStrict()
    {
        ConnectionFactor::enabledSqlLog();
        $bathUpdate = new BatchUpdate();
        $condition  = new Condition('AND');
        $condition->condition('name', '20170517--0938%', 'like');
        $data       = [
            'age' => 170938100,
            'uid' => 170938200,
            'created' => '2017-05-17 09:38',
        ];
        $bathUpdate->addData($condition, $data);
        $data = [
            'age' => 170938300,
            'uid' => 170938400,
            'created' => '2017-05-17 09:38',
        ];
        $bathUpdate->addData($condition, $data);
        $obj    = new Update(ConnectionFactor::getInstance(), 'test');
        $result = $obj->updateAll($bathUpdate, true);
        $this->assertEquals(2, $result);
    }
}