<?php
namespace Wei\Base\Tests\Models;

use Wei\Base\DB\Model\Model;
use Wei\Base\Models\Demo1Model;
use Wei\Base\Models\Demo2Model;
use Wei\Base\Tests\WeiTestCase;

/**
 * 测试模型1
 *
 * Class Demo2ModelTest
 * @package Wei\Base\Tests\Models
 */
class Demo2ModelTest extends WeiTestCase
{
    public static function setUpBeforeClass()
    {
        Demo2Model::getModel()->where([
            'title' => [
                'op' => 'like',
                'demo2-20170410%',
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
            'title' => 'demo2-20170410--1426',
            'created' => '2017-04-10 14:26:01',
        ];
        $id = Demo2Model::getModel()->insert($data);
        return $id;
    }

    /**
     * 测试通过主键查找
     * @depends testInsert
     */
    public function testFindOneByPk($id)
    {
        $data = Demo2Model::getModel()->findOneByPk($id);
        $this->assertEquals('demo2-20170410--1426', $data['title']);
        $this->assertEquals('2017-04-10 14:26:01', $data['created']);
    }
}