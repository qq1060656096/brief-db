<?php
namespace Wei\Base\Tests\Models;

use Wei\Base\DB\Model\Model;
use Wei\Base\Models\Demo1Model;
use Wei\Base\Models\Demo2Model;
use Wei\Base\Tests\WeiTestCase;

/**
 * 测试模型1
 *
 * Class Demo1ModelTest
 * @package Wei\Base\Tests\Models
 */
class Demo1ModelTest extends WeiTestCase
{
    public static function setUpBeforeClass()
    {
        Demo1Model::getModel()->where([
            'title' => [
                'op' => 'like',
                'demo1-20170410%',
            ],
        ])->delete();
    }

    /**
     * 测试插入数据
     * @return int|string
     */
    public function testInsert()
    {
        $data = [
            'title' => 'demo1-20170410--1150',
            'created' => '2017-04-10 11:50:01',
        ];
        $id = Demo1Model::getModel()->insert($data);
        return $id;
    }

    /**
     * 测试通过主键查找
     * @depends testInsert
     */
    public function testFindOneByPk($id)
    {
        $data = Demo1Model::getModel()->findOneByPk($id);
        $this->assertEquals('demo1-20170410--1150', $data['title']);
        $this->assertEquals('2017-04-10 11:50:01', $data['created']);
    }
}