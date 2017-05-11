<?php
namespace Wei\Base\Tests\Database\Query;

use Wei\Base\Database\Query\ConnectionFactor;
use Wei\Base\Database\Query\Update;
use Wei\Base\Tests\WeiTestCase;

class UpdateTest extends WeiTestCase
{
    public function test()
    {
        print_r(ConnectionFactor::getInstance());
        $obj = new Update(ConnectionFactor::getInstance(), 'test');
        $obj->condition('id', 1);
        $obj->save(['id' => '2']);
        print_r($obj);
        $this->assertTrue(true);
    }
}