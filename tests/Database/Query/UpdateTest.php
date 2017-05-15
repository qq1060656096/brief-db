<?php
namespace Wei\Base\Tests\Database\Query;

use Wei\Base\Database\Query\ConnectionFactor;
use Wei\Base\Database\Query\Update;
use Wei\Base\Tests\WeiTestCase;

class UpdateTest extends WeiTestCase
{
    public function test()
    {

        $obj = new Update(ConnectionFactor::getInstance(), 'test');
        $obj->condition('`name`', '20170515--2122');
        ConnectionFactor::enabledSqlLog();
        $obj->from('test');
        $obj->save(['age' => '201705152143']);

        print_r(ConnectionFactor::getLastRawSql());
        $this->assertTrue(true);
    }
}