<?php
namespace Wei\Base\Tests\LimitFrequency;

use Wei\Base\Exception\LimitFrequencyException;
use Wei\Base\LimitFrequency\LimitFrequencyOperation;
use Wei\Base\Tests\WeiTestCase;

/**
 * 限制访问频率操作
 *
 * Class LimitFrequencyData
 * @package Wei\Base\LimitFrequency
 */
class LimitFrequencyOperationTest extends WeiTestCase
{
    /**
     * 测试是否锁定
     */
    public function testIsLock()
    {
        $obj2 = new LimitFrequencyOperation();

        $obj = new LimitFrequencyOperation();
        $this->assertFalse($obj->isLock());
        //锁定1秒
        $obj->setLock(1);
        //永久锁定
        $obj2->setLock(0);
//        print_r($obj);
        $this->assertTrue($obj->isLock());
        $this->assertTrue($obj->isLock());
        sleep(2);
//        print_r(time());
        $this->assertFalse($obj->isLock());
        $this->assertTrue($obj2->isLock());
    }

    /**
     * 设置值
     */
    public function testSetValue()
    {
        $obj = new LimitFrequencyOperation();
        $obj->setValue('30', 1);
        $this->assertEquals('30', $obj->getValue());
    }
    /**
     * 测试访问频率
     */
    public function testGetFrequency()
    {
        $obj = new LimitFrequencyOperation();
        $this->assertFalse($obj->getValue());
        //访问频率1秒
        $value = $obj->getFrequency('1060656096@qq.com', 1);
        $this->assertEquals('1', $value);
        $value = $obj->getFrequency('1060656096@qq.com', 1);
        $value = $obj->getFrequency('1060656096@qq.com', 1);
        $this->assertEquals('3', $value);
        sleep(2);
        $value = $obj->getFrequency('1060656096@qq.com', 1);
        $this->assertEquals('1', $value);
    }


}