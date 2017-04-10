<?php
namespace Wei\Base\Tests\Models;

use Wei\Base\DB\Model\Model;
use Wei\Base\Tests\WeiTestCase;

/**
 * 测试空模型
 *
 * Class ModelTest
 * @package Wei\Base\Tests\Models
 */
class ModelTest extends WeiTestCase
{
    public static function setUpBeforeClass()
    {

        Model::getModel()->setTable('demo1')->where([
            'title' => [
                'op' => 'like',
                'ModelTest-demo1-20170410%',
            ],
        ])->delete();

        Model::getModel()->setTable('demo2')->where([
            'title' => [
                'op' => 'like',
                'ModelTest-demo2-20170410%',
            ],
        ])->delete();
    }

    /**
     * 测试Demo1插入数据
     * @return int|string
     */
    public function testDemo1Insert()
    {
        $data   = [
            'title' => 'ModelTest-demo1-20170410--1437',
            'created' => '2017-04-10 14:37:01',
        ];
        $id     = Model::getModel()->setTable('demo1')->insert($data);
        $data   = Model::getModel()->setTable('demo1')->where(['did' => $id])->findOne();
        $this->assertEquals('ModelTest-demo1-20170410--1437', $data['title']);
        $this->assertEquals('2017-04-10 14:37:01', $data['created']);
    }

    /**
     * 测试Demo2插入数据
     * @return int|string
     */
    public function testDemo2Insert()
    {
        $data   = [
            'title' => 'ModelTest-demo2-20170410--1451',
            'created' => '2017-04-10 14:51:01',
        ];
        $id     = Model::getModel()->setTable('demo2')->insert($data);
        $data   = Model::getModel()->setTable('demo2')->where(['did' => $id])->findOne();
        $this->assertEquals('ModelTest-demo2-20170410--1451', $data['title']);
        $this->assertEquals('2017-04-10 14:51:01', $data['created']);
    }
}