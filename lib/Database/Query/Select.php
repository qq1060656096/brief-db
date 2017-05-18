<?php
namespace Wei\Base\Database\Query;

use Doctrine\DBAL\Connection;
use Wei\Base\Common\ArrayLib;
use Wei\Base\Exception\BaseException;

/**
 * 查询
 * Class Select
 * @package Wei\Base\Database\Query
 */
class Select extends Query
{
    /**
     * 查询字段
     * @var array()
     */
    protected $fields = array();

    /**
     * 关联查询
     *
     * @var array
     */
    protected $join = array();

    /**
     * 查询分组
     *
     * @var array
     */
    protected $group = array();

    /**
     * 排序
     * @var array
     */
    protected $order = array();


    /**
     * 返回记录条数
     *
     * @var integer
     */
    protected $limit = null;
    /**
     * 从那条记录还是查询
     * @var integer
     */
    protected $offset = null;

    /**
     * 编译后查询数组
     * @var array
     */
    protected $queryArr = array();
    /**
     * 编译后参数
     * @var array
     */
    protected $arguments = array();

    /**
     * 初始化
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $table 表名
     */
    public function __construct(Connection $connection, $table)
    {
        parent::__construct($connection);
        $this->table = $table;
        $this->condition = new Condition('AND');
    }

    /**
     * 设置查询[select]字段
     *
     * @param string|array $fields
     * @return $this 失败抛出异常
     * @throws \Exception
     */
    public function fields($fields)
    {
        $this->addFields($fields);
        return $this;
    }

    /**
     * 获取字段细信息
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
    /**
     * 添加查询[select]字段
     *
     * @param string|array $fields 查询字段
     * @return $this 失败抛出异常
     * @throws QueryException 异常
     */
    public function addFields($fields)
    {
        $fieldsArr = $this->parseFields($fields);
        !is_array($this->fields) ? $this->fields = [] : null;
        $this->fields = array_unique(ArrayLib::array_add($this->fields, $fieldsArr));
        return $this;
    }

    /**
     * 解析查询字段
     *
     * @param string|array $fields 查询字段
     * @return array 成功数组,失败异常
     * @throws QueryException 异常
     */
    public function parseFields($fields)
    {
        switch (true) {
            case is_array($fields):
                $fields = array_values($fields);
                break;
            case is_string($fields):
                $fields = preg_split('/\s*,\s*/', trim($fields), -1, PREG_SPLIT_NO_EMPTY);
                break;
            default:
                throw new QueryException('', QueryException::SELECT_NOT_PARSE);
                break;
        }
        return $fields ? $fields :[];
    }


    /**
     * 关联查询
     *
     * @param string $type 关联类型
     * @param string $table 表名
     * @param string $condition 关联条件(on条件)
     * @param array $arguments 参数
     * @return $this
     */
    public function join($type, $table, $condition, $arguments = array())
    {
        $this->join[] = [
            'type' => $type,
            'table' => $table,
            'condition' => $condition,
            'arguments' => $arguments,
        ];
        return $this;
    }

    /**
     * 内联查询
     *
     * @param string $type 关联类型
     * @param string $table 表名
     * @param string $condition 关联条件(on条件)
     * @param array $arguments 参数
     * @return $this
     */
    public function innerJoin($table, $condition, $arguments = array())
    {
        return $this->join('INNER JOIN', $table, $condition, $arguments);
    }
    /**
     * 左联查询
     *
     * @param string $type 关联类型
     * @param string $table 表名
     * @param string $condition 关联条件(on条件)
     * @param array $arguments 参数
     * @return $this
     */
    public function leftJoin($table, $condition, $arguments = array())
    {
        return $this->join('LEFT JOIN', $table, $condition, $arguments);
    }

    /**
     * 右联查询
     *
     * @param string $type 关联类型
     * @param string $table 表名
     * @param string $condition 关联条件(on条件)
     * @param array $arguments 参数
     * @return $this
     */
    public function rightJoin($table, $condition, $arguments = array())
    {
        return $this->join('RIGHT JOIN', $table, $condition, $arguments);
    }

    /**
     * 获取关联join
     *
     * @return array
     */
    public function getJoin()
    {
        return $this->join;
    }

    /**
     * 排序查询
     *
     * @param string $field 字段名
     * @param string $direction 排序类型("ASC和DESC"默认ASC)
     * @return $this
     */
    public function orderBy($field, $direction = 'ASC') {
        $this->order[$field] = $direction;
        return $this;
    }

    /**
     * 获取排序
     */
    public function getOrderBy()
    {
        return $this->order;
    }

    /**
     * 获取排序字符串
     * @return string
     */
    public function getOrderByString()
    {
        $order = $this->getOrderBy();
        $arr = [];
        foreach ($order as $field => $direction) {
            $arr[] = $field.' '. $direction;
        }
        return implode(",", $arr);
    }

    /**
     * 分组
     *
     * @param string $field 字段名
     * @return $this
     */
    public function groupBy($field)
    {
        $this->group[$field] = $field;
        return $this;
    }

    /**
     * 获取分组
     *
     * @return array
     */
    public function getGroupBy()
    {
        return $this->group;
    }
    /**
     * 设置偏移
     *
     * @param integer $offset 偏移,从0条开始
     * @return $this
     */
    public function offset($offset)
    {

        $this->offset = max(0, intval($offset));
        return $this;
    }

    /**
     * 获取offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * 设置显示条数
     *
     * @param integer $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = max(0, intval($limit));
        return $this;
    }

    /**
     * 获取limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * 获取编译后查询数组
     * @return array
     */
    public function getQueryArr()
    {
        return $this->queryArr;
    }

    /**
     * 获取编译后参数
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }


    /**
     * 查询多行数据
     *
     * @return array
     */
    public function findAll()
    {
        $this->compile();
        $queryArr   = $this->queryArr;
        return $this->connection->fetchAll(implode(" ", $queryArr), $this->arguments);
    }

    /**
     * 查询单行数据
     *
     * @return array
     */
    public function findOne()
    {
        $this->compile();
        $queryArr   = $this->queryArr;
        $queryArr['limit'] = 'LIMIT 1';
        $arguments  = $this->arguments;
        return $this->connection->fetchAssoc(implode(" ", $this->queryArr), $this->arguments);
    }

    /**
     * 查询总数
     * @return integer
     */
    /**
     * 查询总条数
     * @return mixed
     */
    public function findCount()
    {
        $this->compile();
        $queryArr   = $this->queryArr;
        $queryArr['field'] = 'count(*)';
        unset($queryArr['limit']);
        return $this->connection->fetchColumn(implode(" ", $queryArr), $this->arguments);
    }

    /**
     * 编译
     */
    public function compile()
    {

        $fields     = $this->getFields();
        $this->condition->compile();
        $arguments  = [];
        $queryArr['select'] = 'SELECT';
        $queryArr['field']  = $fields ? implode(',', $fields): '*';
        $queryArr['from']   = 'FROM';
        $queryArr['table']  = $this->getFrom();

        $wherStr = (string) $this->condition;
        $wherStr ? $queryArr['where'] = 'WHERE '. $wherStr : null;
        $wherStr ? $arguments = $this->condition->arguments() : null;

        $group = $this->getGroupBy();
        $group ? $queryArr['group'] = "GROUP BY ".implode(',', $group) : null;

        $order  = $this->getOrderByString();
        $order ? $queryArr['order'] = "ORDER BY ".$order : null;

        $offset = $this->getOffset();
        $limit  = $this->getLimit();
        switch (true) {
            case $offset && $limit:
                $queryArr['limit'] = "LIMIT {$offset},$limit";
                break;
            case $limit:
                $queryArr['limit'] = "LIMIT $limit";
                break;
            default:
                break;
        }
        $this->queryArr     = $queryArr;
        $this->arguments    = $arguments;
    }
}
