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
    public $join = array();

    /**
     * 查询分组
     *
     * @var array
     */
    public $group = array();

    /**
     * 排序
     * @var array
     */
    public $order = array();


    /**
     * 返回记录条数
     *
     * @var integer
     */
    public $limit = null;
    /**
     * 从那条记录还是查询
     * @var integer
     */
    public $offset = null;

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
}
