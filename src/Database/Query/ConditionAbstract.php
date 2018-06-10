<?php
namespace Zwei\BriefDB\Database\Query;

use Zwei\BriefDB\Common\ArrayLib;

/**
 * 条件抽象类
 *
 * Class ConditionAbstract
 * @package Zwei\BriefDB\DB
 */
abstract class ConditionAbstract implements \Countable
{
    /**
     * 条件数组
     *
     * @var array
     */
    protected $conditions = array();


    /**
     * 条件字符串
     *
     * @var array
     */
    protected $conditionString = null;

    /**
     * 参数数组
     *
     * @var array
     */
    protected $arguments = array();


    /**
     * 初始化
     * @param string $conditionOperator 条件操作符(and或者or)
     */
    public function __construct($conditionOperator)
    {
        $this->conditions['#conditionOperator'] = $conditionOperator;
    }

    /**
     * 添加条件
     *
     * @param string $field 字段
     * @param string|integer|float $value 字段值
     * @param mixed $expressionOperator 表达式操作符(=,>=,in等)
     * @return $this
     */
    public abstract function condition($field, $value = NULL, $expressionOperator = NULL);

    /**
     * 添加复杂的条件
     *
     * @param string|Condition $snippet 小片段
     * @param array|null $args 参数
     * @return $this
     */
    public abstract function conditionComplex($snippet, $args);

    /**
     * 设置字段为null
     * @param string $field 字段名
     * @return $this
     */
    public  abstract function isNull($field);

    /**
     * 设置字段not null条件
     *
     * @param string $field 字段名
     * @return $this
     */
    public abstract function isNotNull($field);


    /**
     * 编译
     *
     * @return $this
     */
    public abstract function compile();

    /**
     * 获取数量
     *
     * @return int
     */
    public function count()
    {
        return count($this->conditions) - 1;
    }

    /**
     * 获取参数
     *
     * @return array
     */
    public function arguments() {
        return $this->arguments;
    }

    /**
     * 获取条件字符串
     *
     * @return array
     */
    public function __toString() {
        return $this->conditionString;
    }
}