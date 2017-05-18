<?php
namespace Wei\Base\Database\Query;

use Doctrine\DBAL\Connection;

/**
 * 查询抽象类
 *
 * Class Query
 * @package Drupal\Core\Database\Query
 */
abstract class Query
{
    /**
     * 表名
     * @var null
     */
    protected $table = null;

    /**
     * 数据库连接
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * 条件
     *
     * @var ConditionAbstract
     */
    protected $condition;

    /**
     * 初始化
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * 设置表名
     *
     * @param string $table 表名
     * @return $this
     */
    public function from($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * 添加条件
     *
     * @param string $field 字段
     * @param string|integer|float $value 字段值
     * @param mixed $expressionOperator 表达式操作符(=,>=,in等)
     * @return $this
     */
    public function condition($field, $value = NULL, $operator = NULL)
    {
        $this->condition->condition($field, $value, $operator);
        return $this;
    }

    /**
     * 添加复杂的条件
     *
     * @param string|Condition $snippet 小片段
     * @param array|null $args 参数
     * @return $this
     */
    public function conditionComplex($snippet, $args)
    {
        $this->condition->conditionComplex($snippet, $args);
        return $this;
    }

    /**
     * 字段不为null
     */
    public function isNull($field) {
        return $this->condition->condition($field, NULL, 'IS NULL');
    }

    /**
     * 字段为null
     */
    public function isNotNull($field) {
        return $this->condition->condition($field, NULL, 'IS NOT NULL');
    }

    /**
     * 获取表名
     *
     * @return string 成功表名,否则失败
     * @throws QueryException 抛出异常
     */
    public function getFrom()
    {
        $table      = $this->table;
        if (empty($table)) {
            throw new QueryException('', QueryException::TABLE_NAME_NOT_NULL);
        }
        return $table;
    }

}
