<?php
namespace Wei\Base\Database\Query;

use Doctrine\DBAL\Connection;
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
     * @var null
     */
    protected $fields = null;

    /**
     * 关联查询
     *
     * @var null: array
     */
    public $join = null;

    /**
     * 查询分组
     *
     * @var null|array
     */
    public $groupBy = null;

    /**
     * 排序
     * @var null|string
     */
    public $orderBy = null;


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
        $this->fields = $this->parseFields($fields);
        return $this;
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
        $selectFields = $this->parseFields($fields);
        if ($selectFields) {
            $this->fields = $this->select ? array_merge($this->select, $selectFields) : $selectFields;
        }
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
                $arr = preg_split('/\s*,\s*/', trim($fields), -1, PREG_SPLIT_NO_EMPTY);
                $fields = array_unique($arr);
                break;
            default:
                throw new QueryException('', QueryException::SELECT_NOT_PARSE);
                break;
        }
        return $fields;
    }



    /**
     * 添加关联查询
     *
     * @param string $type 关联类型
     * @see Query::RIGHT_JOIN
     * @see Query::LEFT_JOIN
     * @param string $table 表名
     * @param string $on 关联字段
     * @return $this
     */
    public function join($type, $table, $on = '')
    {
        $this->join[] = [$type, $table, $on];
        return $this;
    }

    /**
     * 获取关联[join]
     *
     * @return string
     */
    public function getJoin()
    {
        $str = '';
        switch (true) {
            case is_array($this->join):
                $joinArr = [];
                foreach ($this->join as $key => $row)
                {
                    if (is_array($row) && count($row) == 3) {
                        list($type, $table, $on) = $row;
                        $joinArr[] = $on ? "$type $table on {$on}" : "$type $table";
                    } elseif (is_string($row)) {
                        $joinArr[] = $row;
                    }
                }
                $str = implode(" ", $joinArr);
                break;
            case is_string($this->join):
                $str = $this->join;
                break;
            default:
                break;
        }
        return $str;
    }

    /**
     * 添加内关联查询
     *
     * @param string $table 表名
     * @param string $on 关联字段
     * @return $this
     */
    public function innerJoin($table, $on = '')
    {
        $this->join(self::INNER_JOIN, $table, $on);
        return $this;
    }

    /**
     * 添加左关联查询
     *
     * @param string $table 表名
     * @param string $on 关联字段
     * @return $this
     */
    public function leftJoin($table, $on = '')
    {
        $this->join(self::LEFT_JOIN, $table, $on);
        return $this;
    }

    /**
     * 添加右关联查询
     *
     * @param string $table 表名
     * @param string $on 关联字段
     * @return $this
     */
    public function rightJoin($table, $on = '')
    {
        $this->join(self::RIGHT_JOIN, $table, $on);
        return $this;
    }

    /**
     * 解析SQL分组查询[group by]
     *
     * @param string|array $groupBy 分组查询
     * @return arrray 成功数组,失败抛出异常
     * @throws QueryException 抛出异常
     */
    public function parseGoupBy($groupBy)
    {
        $params = [];
        switch (true) {
            case is_array($groupBy):
                $inArr = ['desc', 'asc'];
                foreach ($groupBy as $key => $row) {
                    if (is_numeric($key)) {
                        $params[] = is_array($row) ? $this->parseGoupBy($row): "$row";
                    } else if(is_string($key) && in_array(strtolower(trim($row)), $inArr)) {
                        $params[] = "$key {$row}";
                    }
                }
                break;
            case is_string($groupBy):
            case is_numeric($groupBy):
                $params = preg_split('/\s*,\s*/', trim($groupBy), -1, PREG_SPLIT_NO_EMPTY);
                break;
            default:
                throw new QueryException('', QueryException::GROUP_BY_NOT_PARSE);//解析失败
                break;
        }
        return $params;
    }

    /**
     * 获取分组查询
     *
     * @return string
     */
    public function getGroupBy()
    {
        $str = '';
        $params = [];
        switch (true) {
            case is_array($this->groupBy):

                foreach ($this->groupBy as $key => $row)
                {
                    $params[] = $row;
                }
                $str = implode(",", array_pad([], count($params), '?'));
                break;
            case is_string($this->groupBy):
                $str        = '?';
                $params[]   = $this->groupBy;
                break;
            default:
                break;
        }
        $str ? $str = "group by {$str}" : null;
        return [$str, $params];
    }

    /**
     * 设置SQL分组查询[group by]
     *
     * @param string|array $groupBy 分组查询
     * @return $this 失败抛出异常
     * @throws QueryException
     */
    public function groupBy($groupBy)
    {
        $this->groupBy = $this->parseGoupBy($groupBy);
        return $this;
    }

    /**
     * 添加SQL分组查询
     *
     * @param string|array $groupBy 分组查询
     * @return $this 失败抛出异常
     * @throws QueryException 异常
     */
    public function addGroupBy($groupBy)
    {
        $arr = $this->parseGoupBy($groupBy);
        if ($this->groupBy) {
            $this->groupBy = ArrayLib::array_add($this->groupBy, $arr);
        }
        return $this;
    }

    /**
     * 解析SQL排序[order by]
     *
     * @param string|array $groupBy 排序
     * @return arrray 成功数组,失败抛出异常
     * @throws QueryException 抛出异常
     */
    public function parseOrderBy($orderBy)
    {
        switch (true) {
            case is_array($orderBy):
                foreach ($orderBy as $key => $row) {
                    if(is_array($row)){
                        $arr[] = is_array($row) ? $this->parseOrderBy($row) : null;
                    } elseif(is_numeric($key)) {
                        $arr[] = "$row";
                    } elseif (is_string($key)) {
                        $arr[] = "{$key} $row";
                    }
                }
                break;
            case is_string($orderBy):
            case is_numeric($orderBy):
                $arr = preg_split('/\s*,\s*/', trim($orderBy), -1, PREG_SPLIT_NO_EMPTY);
                break;
            default:
                throw new QueryException('', QueryException::ORDER_BY_NOT_PARSE);//解析失败
                break;
        }
        return $arr;
    }

    /**
     * 获取排序
     *
     * @return string
     */
    public function getOrderBy()
    {
        $str = '';
        $params = [];
        switch (true) {
            case is_array($this->orderBy):
                foreach ($this->orderBy as $key => $row)
                {
                    $params[] = $row;
                }
                $str = implode(",", array_pad([], count($params), '?'));
                break;
            case is_string($this->orderBy):
                $str        = '?';
                $params[]   = $this->orderBy;
                break;
            default:
                break;
        }
        $str ? $str = "order by {$str}" : null;
        return [$str, $params];
    }

    /**
     * 设置SQL排序[order by]
     *
     * @param string|array $groupBy 分组查询
     * @return $this 失败抛出异常
     * @throws QueryException
     */
    public function orderBy($orderBy)
    {
        $this->orderBy = $this->parseOrderBy($orderBy);
        return $this;
    }

    /**
     * 添加SQL分组查询
     *
     * @param string|array $groupBy 分组查询
     * @return $this 失败抛出异常
     * @throws QueryException 异常
     */
    public function addOrderBy($orderBy)
    {
        $arr = $this->parseOrderBy($orderBy);
        if ($arr) {
            foreach ($arr as $key => $row) {
                $this->orderBy[] = $row;
            }
        }
        return $this;
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
