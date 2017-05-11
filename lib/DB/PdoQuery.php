<?php
namespace Wei\Base\DB;

use Wei\Base\Common\ArrayLib;
use Wei\Base\DB\Connection;
use Wei\Base\Exception\BaseException;
use Wei\Base\Exception\QueryException;

class PdoQuery
{
    /**
     * 倒序
     */
    const SORT_DESC = 'desc';
    /**
     * 顺序
     */
    const SORT_ASC = 'asc';

    /**
     * and条件
     *
     * @var string
     */
    const WHERE_AND = 'and';

    /**
     * or条件
     * @var string
     */
    const WHERE_OR = 'or';

    /**
     * 右连接
     * @var string
     */
    const LEFT_JOIN = 'left join';

    /**
     * 内连接
     * @var string
     */
    const INNER_JOIN = 'inner join';
    /**
     * 左连接
     * @var string
     */
    const RIGHT_JOIN = 'right join';

    /**
     * where 操作符in
     */
    const WHERE_OP_IN = 'in';
    /**
     * 查询字段
     *
     * @var null|array
     */
    public $select = null;

    /**
     * 表名
     *
     * @var null|string
     */
    public $from = null;

    /**
     * 关联查询
     *
     * @var null: array
     */
    public $join = null;

    /**
     * where条件
     *
     * @var null|string
     */
    public $where = null;

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
     * SQL日志
     *
     * @var \Doctrine\DBAL\Logging\DebugStack
     */
    public $logger = null;

    /**
     * 数据库连接
     *
     * @var \Doctrine\DBAL\Connections\MasterSlaveConnection|null
     */
    protected $db = null;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /**
     * 获取DB
     *
     * @param \Doctrine\DBAL\Connection|null $db
     * @return \Doctrine\DBAL\Connection
     */
    public function getDB($db = null)
    {
        return $db ? $db:$this->db;
    }
    /**
     * 开启sql日志
     *
     * @see Query::getLastRawSql()
     * @return $this
     */
    public function enabledSqlLog()
    {
        if (!$this->logger) {
            $this->logger = new \Doctrine\DBAL\Logging\DebugStack();
            $this->getDB()->getConfiguration()->setSQLLogger($this->logger);
        }
        return $this;
    }

    /**
     * 获取最后执行sql
     *
     * @see Query::enabledSqlLog()
     * @return array|null
     */
    public function getLastRawSql()
    {
        if (!$this->logger) {
            null;
        }
        if(!isset($this->logger->queries[$this->logger->currentQuery])){

            $rawSql['msg']           = 'queries 没有数据';
            return $rawSql;
        }

        $rawSql = $this->logger->queries[$this->logger->currentQuery];
        $tmp['currentQuery'] = $rawSql;
        $query      = $tmp['currentQuery'];
        $tmp_sql    = $query['sql'];
        $tmp_params = $query['params'];
        count($tmp_params)<1? $tmp_params=[]:null;
        foreach ($tmp_params as $key => $value) {
            if ($value === null) {
                $tmp_sql = preg_replace('/\?/', "NULL", $tmp_sql, 1);
            } else {
                $tmp_sql = preg_replace('/\?/', "'{$value}'", $tmp_sql, 1);
            }

        }
        $tmp['rawSql']         = $tmp_sql;

        return $tmp;
    }

    /**
     * 获取查询字段
     *
     * @return string 字段信息
     */
    public function getSelect()
    {
        $str = '';
        $params = [];
        switch (true) {
            case is_array($this->select):
                $str = implode(',', array_pad([], count($this->select), '?'));
                $params   = $this->select;
                break;
            case is_string($this->select):
                $str        = '?';
                $params[]   = $this->select;
                break;
            default:
                $str = '?';
                $params[] = '*';
                break;
        }
        return [$str, $params];
    }
    /**
     * 设置查询[select]字段
     *
     * @param string|array $fields
     * @return $this 失败抛出异常
     * @throws \Exception
     */
    public function select($fields)
    {
        $this->select = $this->parseSelect($fields);
        return $this;
    }
    /**
     * 添加查询[select]字段
     *
     * @param string|array $fields 查询字段
     * @return $this 失败抛出异常
     * @throws QueryException 异常
     */
    public function addSelect($fields)
    {
        $selectFields = $this->parseSelect($fields);
        if ($selectFields) {
            $this->select = $this->select ? array_merge($this->select, $selectFields) : $selectFields;
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
    public function parseSelect($fields)
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
     * 设置查询表名
     * @param string $table
     * @return $this 失败抛出异常
     * @throws QueryException 异常
     */
    public function from($table)
    {
        $table = trim($table);
        if (empty($table) || !is_string($table)) {
            throw new QueryException('', QueryException::TABLE_NAME_NOT_NULL);
        }
        $this->from = $table;
        return $this;
    }

    /**
     * 获取表名
     *
     * @return string 成功表名,否则失败
     * @throws QueryException 抛出异常
     */
    public function getFrom()
    {
        $table      = $this->from;
        if (empty($table)) {
            throw new QueryException('', QueryException::TABLE_NAME_NOT_NULL);
        }
        return $table;
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
     * 获取where条件
     *
     * @return null|string
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * 设置[where]条件
     *
     * @param string|array $where
     * @return $this 失败抛出异常
     * @throws QueryException 异常
     */
    public function where($where)
    {
        $this->where = $this->parseWhere($where);
        return $this;
    }
    /**
     * 添加and[where]条件
     *
     * @param string|array $where 条件
     * @return $this 失败抛出异常
     * @throws QueryException 异常
     */
    public function andWhere($where)
    {
        $arr    = $this->parseWhere($where);
        $str    = '';
        $params = [];
        if ($arr) {
            list($str, $params) = $arr;
        }
        if ($this->where) {
            list($oldStr, $oldParams) = $this->where;
            $this->where = [$oldStr.' and '.$str, ArrayLib::array_add($oldParams, $params)];
        }
        return $this;
    }

    /**
     * 添加or[where]条件
     *
     * @param string|array $where 条件
     * @return $this 失败抛出异常
     * @throws QueryException 异常
     */
    public function orWhere($where)
    {
        $arr    = $this->parseWhere($where);
        $str    = '';
        $params = [];
        if ($arr) {
            list($str, $params) = $arr;
        }
        if ($this->where) {
            list($oldStr, $oldParams) = $this->where;
            $this->where = [$oldStr.' or '.$str, ArrayLib::array_add($oldParams, $params)];
        }
        return $this;
    }

    /**
     * 变量转 where in值[demo:"('1','2','3')"]
     * @param string|array $var 变量
     * @return string
     */
    public function convertToWhereIn($var)
    {
        $str    = '';
        $params = [];
        switch (true) {
            case is_array($var):
                $str = implode(",", array_pad([], count($var), '?'));
                $params = $var;
                break;
            case is_numeric($var):
            case is_string($var):
                $str    = '?';
                $params[] = $var;
                break;
            default:
                break;
        }
        return [$str, $var];
    }
    /**
     * 解析where
     * @param string|array $where 条件
     * @return string 成功返回字符串
     * @throws QueryException 失败抛出异常
     */
    public function parseWhere($where)
    {
        if ($where === null || $where === "" || $where === []) {
            return '';
        }
        switch (true) {
            case is_string($where)://where字符串直接返回
                $whereStr   = '?';
                $params[]   = $where;
                return [$whereStr, $params];
                break;
            case is_array($where):
                $whereStr   = '';
                $tmpArr     = [];
                $params     = [];
                foreach ($where as $key => $row) {
                    $on = isset($row['on']) && strtolower($row['on'])==self::WHERE_OR ? self::WHERE_OR: self::WHERE_AND;
                    $op = isset($row['op']) && $row['op'] ? $row['op']: '=';
                    $op = trim(strtolower($op));
                    if (is_array($row)) {
                        unset($row['op'], $row['on']);
                    }
                    $rowLen = is_array($row) ? count($row):0;
                    //是否设置过value值
                    $is_set_value = true;
                    switch (true) {
                        case isset($row['rawValue']):
                            $value = $row['rawValue'];
                            break;
                        case isset($row['value']):
                            $value = $row['value'];
                            break;
                        case $rowLen == 1:
                            $value = array_shift($row);
                            break;
                        case is_numeric($row) || is_string($row):
                            $value = $row;
                            break;
                        default:
                            $is_set_value = false;
                            break;
                    }
                    if ($is_set_value === false) {
                        continue;
                    }

                    if (isset($row['rawValue'])) {
                        $value = $row['rawValue'];
                    } elseif ($op == self::WHERE_OP_IN) {
                         $value = $this->convertToWhereIn($value);
                    } else {

                    }
                    // where in操作
                    if (!isset($row['rawValue']) && $op == self::WHERE_OP_IN) {
                        list($row_in_str, $row_in_params) = $value;
                        if ($row_in_str) {
                            $tmp    = implode(" ", [$key, $op, "({$row_in_str})"]);
                            $params = ArrayLib::array_add($params, $row_in_params);
                            $whereStr .= empty($whereStr) ? $tmp : " $on ".$tmp;
                        }
                    } else {
                        $tmp = implode(" ", [$key, $op, '?']);
                        $params[] = $value;
                        $whereStr .= empty($whereStr) ? $tmp : " $on ".$tmp;
                    }

                }
                return [$whereStr, $params];
                break;
            default:
                throw new QueryException(QueryException::WHERE_NOT_PARSE);
                break;
        }
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
    /**
     * 获取查询部分sql
     *
     *
     * @return string
     * @throws QueryException
     */
    public function getSelectRawSqlPart()
    {
//        $strFields  = $this->getSelect();
//        $table      = $this->getFrom();
//        $strWhere   = $this->getWhere();
//        $strJoin    = $this->getJoin();
//        $strGroupBy = $this->getGroupBy();
//        $strOrderBy = $this->getOrderBy();
//        $sql = "select {$strFields} from {$table}{$strWhere}{$strGroupBy}{$strOrderBy}";

        $arr[]  = $this->getSelect();
        $arr[]  = ['from ? ',[$this->getFrom()]];
        $arr[]  = $this->getWhere();
        $arr[]  = $this->getJoin();
        $arr[]  = $this->getGroupBy();
        $arr[]  = $this->getOrderBy();
        $sql = 'select ';
        $allParams = [];
        foreach ($arr as $key=>$value) {
            if (count($value) == 2) {
                list($str, $params) = $value;
                $sql .=  $str;
                $allParams = ArrayLib::array_add($allParams, $params);

            }
        }
        return [$sql, $allParams];
    }

    /**
     * 获取单行单列列sql[]
     * @param string $field 字段名
     * @return string 成功string,否则失败
     * @throws QueryException 抛出异常
     */
    public function getColumnRawSqlPart($field)
    {
//        $strFields  = "$field";
//        $table      = $this->getFrom();
//        $strWhere   = $this->getWhere();
//        $strJoin    = $this->getJoin();
//        $strGroupBy = $this->getGroupBy();
//        $strOrderBy = $this->getOrderBy();
//        $sql = "select {$strFields} from {$table}{$strWhere}{$strGroupBy}{$strOrderBy}";

        $arr[]  = ['?', [$field]];
        $arr[]  = ['from ? ',[$this->getFrom()]];
        $arr[]  = $this->getWhere();
        $arr[]  = $this->getJoin();
        $arr[]  = $this->getGroupBy();
        $arr[]  = $this->getOrderBy();
        $sql = 'select ';
        $allParams = [];
        foreach ($arr as $key=>$value) {
            if (count($value) == 2) {
                list($str, $params) = $value;
                $sql .=  $str;
                $allParams = ArrayLib::array_add($allParams, $params);

            }
        }
        return [$sql, $allParams];
    }

    /**
     * 获取删除sql部分
     *
     * @return string
     */
    public function getDeleteRawSqlPart()
    {
//        $table      = $this->getFrom();
//        $strWhere   = $this->getWhere();
//        $strJoin    = $this->getJoin();
//        $strOrderBy = $this->getOrderBy();
//        $sql = "delete from {$table}{$strWhere}{$strOrderBy}";
//        return $sql;

        $arr[]  = ['from ? ',[$this->getFrom()]];
        $arr[]  = $this->getWhere();
        $arr[]  = $this->getJoin();
        $arr[]  = $this->getOrderBy();
        $sql = 'delete ';
        $allParams = [];
        foreach ($arr as $key=>$value) {
            if (count($value) == 2) {
                list($str, $params) = $value;
                $sql .=  $str;
                $allParams = ArrayLib::array_add($allParams, $params);

            }
        }
        return [$sql, $allParams];
    }

    /**
     * 获取SQL limit部分
     *
     * @return string
     */
    public function getRawLimitPart()
    {
        $strLimit = '';
        switch (true) {
            case $this->offset && $this->limit:
                $strLimit = " limit {$this->offset},{$this->limit}";
                break;
            case $this->limit:
                $strLimit = " limit {$this->limit}";
                break;
        }
        return $strLimit;
    }

    /**
     * 返回单行记录
     *
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return array|mixed
     */
    public function findOne($db = null)
    {
        $sql = $this->getSelectRawSqlPart()." limit 1";
        return $this->getDB($db)->fetchAssoc($sql);
    }

    /**
     * 返回多行记录
     *
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return array|mixed
     */
    public function findAll($db = null)
    {
        $sql = $this->getSelectRawSqlPart().$this->getRawLimitPart();
        return $this->getDB($db)->fetchAll($sql);
    }

    /**
     * 获取最大值
     *
     * @param string $field 字段名
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return mixed
     */
    public function max($field, $db = null)
    {
        $sql = $this->getColumnRawSqlPart("max({$field})");
        return $this->getDB($db)->fetchColumn($sql);
    }

    /**
     * 获取最小值
     *
     * @param string $field 字段名
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return mixed
     */
    public function min($field, $db = null)
    {
        $sql = $this->getColumnRawSqlPart("min({$field})");
        return $this->getDB($db)->fetchColumn($sql);
    }

    /**
     * 获取总和
     *
     * @param string $field 字段名
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return mixed
     */
    public function sum($field, $db = null)
    {
        $sql = $this->getColumnRawSqlPart("sum({$field})");
        return $this->getDB($db)->fetchColumn($sql);
    }

    /**
     * 获取平均值
     *
     * @param string $field 字段名
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return mixed
     */
    public function average($field, $db = null)
    {
        $sql = $this->getColumnRawSqlPart("avg({$field})");
        return $this->getDB($db)->fetchColumn($sql);
    }

    /**
     * 获取总条数
     *
     * @param string $field 字段名,默认空
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return mixed
     */
    public function count($field = '', $db = null)
    {
        $field && is_string($field) ? null : $field = '*';
        $sql = $this->getColumnRawSqlPart("count({$field})");
        return $this->getDB($db)->fetchColumn($sql);
    }

    /**
     * 插入数据
     *
     * @param array $data 键值数据
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return int|string
     */
    public function insert($data, $db = null)
    {
        return $this->getDB($db)->insert($this->getFrom(), $data);
    }

    /**
     * 插入多条数据
     *
     * @param array $rows 多行键值数组
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return int 插入条数
     * @throws QueryException 抛出异常
     */
    public function insertAll($rows, $db = null)
    {
        $table = $this->getFrom();
        //不是数组
        if (!is_array($rows)) {
            throw new QueryException('', QueryException::PARAMS_ILLEGAL);
        }
        //获取插入字段
        $fields         = array_keys(current($rows));
        //插入行字符串
        $insertRowStr   = implode(',', array_pad([], count($fields),'?'));
        $is_set_value   = false;
        //插入的所有值
        $params         = $fields;
//        array_unshift($params, $table);
        //插入字符串
        $insertStrArr   = [];
        foreach ($rows as $key=> $row) {
            if (!is_array($row)) {
                continue;
            }
            $is_set_value   = true;
            foreach ($row as $field => $value) {
                $params[] = $value;
            }
            $insertStrArr[] = '('.$insertRowStr.')';

        }
        $insertStr = implode(',', $insertStrArr);
        $sql = "insert into ?({$insertRowStr}) values{$insertStr}";
//        $result = $this->getDB($db)->executeQuery($sql,$params);

        $stmt = $this->getDB($db)->prepare($sql);
        $stmt->bindValue(null, $table);
        foreach ($params as $name => $value) {
            if (isset($types[$name])) {
                $type = $types[$name];
                list($value, $bindingType) = $this->getBindingInfo($value, $type);
                $stmt->bindValue($name, $value, $bindingType);
            } else {
                $stmt->bindValue($name, $value);
            }
        }

        $result = $stmt->execute();
        return $result;
    }

    /**
     * 获取最后插入id
     *
     * @param \Doctrine\DBAL\Connection 000000000$db 数据库连接
     * @return string
     */
    public function getLastInsertId($db = null)
    {
        return $this->getDB($db)->lastInsertId();
    }

    /**
     * 删除数据[limit限制条数]
     *
     * @param null $db
     * @return int
     */
    public function delete($db = null)
    {
        $sql        = $this->getDeleteRawSqlPart();
        $strLimit   = $this->getRawLimitPart();
        $sql        = "$sql{$strLimit}";
        return $this->getDB($db)->exec($sql);
    }
    /**
     * 更改数据
     *
     * @param mixed $data 数据[键值数组]
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return int
     */
    public function update($data, $db = null)
    {
        $table      = $this->getFrom();
        $strWhere   = $this->getWhere();
        $set        = [];
        $setStr     = '';
        $params     = [];
        switch (true) {
            case is_array($data):
                foreach ($data as $key => $row) {
                    if (isset($row['raw'])) {
                        $set[]      = "$key = ?";
                        $params[]   = $row['raw'];
                    } else {
                        $set[]      = "$key = ?";
                        $params[]   = $row;
                    }
                }
                $setStr = implode(',', $set);
                break;
            case is_object($data):
                break;
            default:
                $setStr     = '?';
                $params[]   = $data;
                break;
        }
        $setStr ? $setStr = 'set '.$setStr : null;
        $sql = "update {$table} {$setStr}{$strWhere}";
        $result = $this->getDB($db)->executeUpdate($sql);
        return $result;
    }


    /**
     * 批量更改数据
     *
     * @param BatchOperation $batchOperation 批量更新数据
     * @param \Doctrine\DBAL\Connection $db 数据库连接
     * @return int 更新条数
     * @throws BaseException 抛出异常
     */
    public function updateAll(BatchOperation $batchOperation, $db = null)
    {
        $this->getDB($db)->beginTransaction();
        $rows       = $batchOperation->getData();
        $updateRows = 0;//更新行数
        $query      = new Query();
        $query->from($this->getFrom());
        foreach ($rows as $key => $row) {
            if(isset($row['where']) && isset($row['data'])){
                $query->where($row['where']);
                $result = $query->update($row['data']);
                $result ? $updateRows++ : null;
            }
        }
        $this->getDB($db)->commit();
        return $updateRows;
    }
}