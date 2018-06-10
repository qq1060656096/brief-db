<?php
namespace Zwei\BriefDB\Tests\Database\Query;

use Zwei\BriefDB\Database\Query\Condition;
use Zwei\BriefDB\Tests\WeiTestCase;

class ConditionTest extends WeiTestCase
{
    /**
     * 测试in条件
     */
    public function testConditionIn()
    {
        $condition = new Condition('and');
        $condition->condition('age', [2,3,4,5]);
        $condition->compile();
        $this->assertEquals('age IN ( ?,?,?,? )', (string)$condition);
        $this->assertEquals([2,3,4,5], $condition->arguments());
    }
    /**
     * 测试简单条件
     */
    public function testCondition()
    {
        $condition = new Condition('and');
        $condition->condition('age', 10, '>=');
        $condition->compile();
        $this->assertEquals('age >= ?', (string)$condition);
        $this->assertEquals([10], $condition->arguments());


        $condition->condition('uid', 20, '>=');
        $condition->condition('uid', 30, '<=');
        $condition->compile();
        $this->assertEquals('age >= ? and uid >= ? and uid <= ?', (string)$condition);
        $this->assertEquals([10, 20, 30], $condition->arguments());

        $condition = new Condition('or');
        $condition->condition('name', 'xiaoxiao%', 'like');
        $condition->condition('age', 40, '>=');
        $condition->condition('uid', 50, '<=');
        $condition->compile();
        $this->assertEquals('name LIKE ? or age >= ? or uid <= ?', (string)$condition);
        $this->assertEquals(['xiaoxiao%', 40, 50], $condition->arguments());
    }

    /**
     * 测试复杂的条件
     */
    public function testConditionComplex()
    {
        //测试复杂条件
        $condition = new Condition('and');
        $condition->conditionComplex("(age >= ? and age <= ?) or name like ?", [60, 70, 'z%']);
        $condition->compile();
        $this->assertEquals('((age >= ? and age <= ?) or name like ?)', (string)$condition);
        $this->assertEquals([60, 70, 'z%'], $condition->arguments());


        //多个Condition组合查询
        $condition1 = new Condition('and');
        $condition1->condition('age', 80, '>=');
        $condition1->condition('age', 90, '<=');
        $condition1->compile();

        $condition2 = new Condition('and');
        $condition2->condition('name', 'x%', 'like');
        $condition2->compile();

        $condition = new Condition('or');
        $condition->conditionComplex((string)$condition1, $condition1->arguments());
        $condition->conditionComplex((string)$condition2, $condition2->arguments());
        $condition->compile();

        $this->assertEquals('(age >= ? and age <= ?) or (name LIKE ?)', (string)$condition);
        $this->assertEquals([80, 90, 'x%'], $condition->arguments());

    }
}