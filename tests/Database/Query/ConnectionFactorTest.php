<?php
namespace Wei\Base\Tests\Database\Query;

use Wei\Base\Database\Driver\DriverName;

use Wei\Base\Database\Query\BatchUpdate;
use Wei\Base\Database\Query\Condition;
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

    public function testDataBaseOperationInstance()
    {
        $mysqlDelete = ConnectionFactor::getDelete(ConnectionFactor::getInstance(), DriverName::MYSQL);
        $this->assertEquals(get_class($mysqlDelete), 'Wei\Base\Database\Driver\mysql\Delete');

        $mysqlInsert = ConnectionFactor::getInsert(ConnectionFactor::getInstance(), DriverName::MYSQL);
        $this->assertEquals(get_class($mysqlInsert), 'Wei\Base\Database\Driver\mysql\Insert');

        $mysqlUpdate = ConnectionFactor::getUpdate(ConnectionFactor::getInstance(), DriverName::MYSQL);
        $this->assertEquals(get_class($mysqlUpdate), 'Wei\Base\Database\Driver\mysql\Update');

        $mysqlSelect = ConnectionFactor::getSelect(ConnectionFactor::getInstance(), DriverName::MYSQL);
        $this->assertEquals(get_class($mysqlSelect), 'Wei\Base\Database\Driver\mysql\Select');
    }

    public function test()
    {
        ConnectionFactor::getInstance();
        ConnectionFactor::enabledSqlLog();
        // 删除
        ConnectionFactor::getDelete(ConnectionFactor::getInstance(), DriverName::MYSQL)
            ->from('test')->condition('name', 'test_01')->delete();

        // 插入
        $insertData = [
            'name' => 'insert01',
            'age' => 1,
            'uid' => 1,
        ];
        ConnectionFactor::getInsert(ConnectionFactor::getInstance(), DriverName::MYSQL)
            ->from('test')->insert($insertData);

        // 批量插入
        $insertData = [
            [
                'name' => 'insertAll01',
                'age' => 1,
                'uid' => 2,
            ],
            [
                'name' => 'insertAll02',
                'age' => 3,
                'uid' => 4,
            ]
        ];
        $result = ConnectionFactor::getInsert(ConnectionFactor::getInstance(), DriverName::MYSQL)
            ->from('test')->insertAll($insertData);

        // 更改
        $updateData = [
            'name' => 'update01',
            'age' => 33,
            'uid' => 333,
        ];
        ConnectionFactor::getUpdate(ConnectionFactor::getInstance(), DriverName::MYSQL)
            ->from('test')->condition('name', 'update01')->update($updateData);


        // 批量更改
        $bathUpdate = new BatchUpdate();
        $condition  = new Condition('AND');
        $condition->condition('name', 'updateBatch01', 'like');
        $data       = [
            'age' => 1815080100,
            'uid' => 18150801200,
            'created' => '2017-05-18 15:08:01',
        ];
        $bathUpdate->addData($condition, $data);
        $data = [
            'age' => 1815080200,
            'uid' => 18150802200,
            'created' => '2017-05-18 15:08:02',
        ];
        $condition  = new Condition('AND');
        $condition->condition('name', 'updateBatch02', 'like');
        $bathUpdate->addData($condition, $data);
        ConnectionFactor::getUpdate(ConnectionFactor::getInstance(), DriverName::MYSQL)
            ->from('test')->updateAll($bathUpdate);


        //查询
        $select = ConnectionFactor::getSelect(ConnectionFactor::getInstance(), DriverName::MYSQL)
            ->fields('name,id')
            ->fields(['uid', 'age'])
            ->from('test')
            ->condition('name', 'ConnectionFactorSelect00%', 'like')
//            ->groupBy('age')
            ->orderBy('id', 'DESC');
        // 查询单行数据
        $row = $select->findOne();
        // 查询多行数据
        $rows = $select->findAll();
        // 查询总条数
        $count = $select->findCount();

        //关联查询
        $select = ConnectionFactor::getSelect(ConnectionFactor::getInstance(), DriverName::MYSQL);
        $select->from('test t1');
        $select->condition('t2.name', 'SelectTest::testFindAll-20170518-1256%', 'like');
        // in查询
        $select->condition('t2.name', ['xiao1','xiao2']);
        $select->fields('*');
        // 左联查询
        $select->leftJoin('test t2', 'on t2.id = t1.id');
        // 右联查询
        $select->rightJoin('test t3', 'on t3.id = t2.id');
        // 内联查询
        $select->innerJoin('test t4', 'on t4.id = t3.id');
        $select->findAll();
//        print_r($row);
//        print_r($rows);
//        var_dump($count);
//        var_dump($result);
//        print_r(ConnectionFactor::getLastRawSql());
        $sql = "SELECT * FROM test t1 LEFT JOIN test t2 on t2.id = t1.id RIGHT JOIN test t3 on t3.id = t2.id INNER JOIN test t4 on t4.id = t3.id WHERE t2.name LIKE ? AND t2.name IN ( ?,? )";
//        print_r($select->getArguments());
//        print_r(ConnectionFactor::getLastRawSql()['currentQuery']['sql']);

        $this->assertEquals($sql, ConnectionFactor::getLastRawSql()['currentQuery']['sql']);

    }

    /**
     *
     */
    public function testCondition()
    {

    }
}