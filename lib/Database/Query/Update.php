<?php
namespace Wei\Base\Database\Query;

use Wei\Base\Common\ArrayLib;


/**
 * 更新
 *
 * Class Update
 * @package Wei\Base\Database\Query
 */
class Update extends Query
{

    /**
     * 表名
     *
     * @var string
     */
    protected $table;


    /**
     * 参数
     *
     * @var array
     */
    protected $arguments = array();

    /**
     * set数组
     * @var array
     */
    protected $setString = null;

    /**
     * 条件
     *
     * @var ConditionAbstract
     */
    protected $condition;

    /**
     * 初始化
     * @param \Doctrine\DBAL\Connections\MasterSlaveConnection $connection
     * @param string $table 表名
     */
    public function __construct(MasterSlaveConnection $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->condition = new Condition('AND');
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
     * 编译data数据
     * @param array $data 数组
     * @return array
     */
    public function compileData($data)
    {
        $setFragment    = [];
        $arguments      = [];
        foreach ($data as $key => $value) {
            $operator   = isset($value['operator']) ? $value['operator'] : '=';
            unset($value['operator']);
            $field      = isset($value['field']) ? $value['field'] : $key;
            unset($value['field']);
            $value      = isset($value['value']) ? $value['value'] : $value;
            unset($value['field']);

            $setFragment[]  = "{$field} {$operator} ?";
            $arguments[]    = $value['value'];
        }
        $this->setString = implode(',', $setFragment);
        $this->arguments = $arguments;
        return [$setFragment, $arguments];
    }

    /**
     * @param array $data
     */
    public function save($data)
    {
        $sql = "UPDATE ? SET {$this->setString}";
        array_unshift($this->arguments, $this->table);
        //设置了条件
        if ($this->condition->count() > 0) {
            $whereStr       = (string)$this->condition->compile();
            $whereArguments = $this->condition->arguments();
            $sql = $sql.' '.$whereStr;
            ArrayLib::array_add($this->arguments, $whereArguments);
        }
        print_r(get_defined_vars());
    }

}
