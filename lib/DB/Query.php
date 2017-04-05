<?php
namespace Wei\DoctrineModel;

use Wei\Base\BaseException;

class Query
{
    /**
     * and条件
     */
    const ON_AND = 'and';

    /**
     * or条件
     */
    const ON_OR = 'or';



    /**
     * @var null|string 查询字段
     */
    public $select = null;

    /**
     * @var null|string 表名
     */
    public $from = null;
    /**
     * @var null|string where条件
     */
    public $where = null;

    /**
     * @var \Doctrine\DBAL\Logging\DebugStack SQL日志
     */
    protected $logger = null;

    /**
     * 开启sql日志
     *
     * @see ModelBase::getLastRawSql()
     * @return bool
     */
    public function enabledSqlLog()
    {
        if (!$this->logger) {
            $this->logger = new \Doctrine\DBAL\Logging\DebugStack();
            $this->DB->getConfiguration()->setSQLLogger($this->logger);
        }
        return true;
    }

    /**
     * 获取最后执行sql
     *
     * @see ModelBase::enabledSqlLog()
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
     * @throws BaseException 异常
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
     * @throws BaseException 异常
     */
    public function parseSelect($fields)
    {
        switch (true) {
            case is_array($fields):
                break;
            case is_string($fields):
                $arr = preg_split('/\s*,\s*/', trim($fields), -1, PREG_SPLIT_NO_EMPTY);
                $fields = array_unique($arr);
                break;
            default:
                throw new BaseException('', BaseException::SELECT_NOT_PARSE);
                break;
        }
        return $fields;
    }

    /**
     * 设置查询表名
     * @param string $tableName
     * @return $this 失败抛出异常
     * @throws BaseException 异常
     */
    public function from($tableName)
    {
        $tableName = trim($tableName);
        if (empty($tableName) || !is_string($tableName)) {
            throw new BaseException('', BaseException::TABLE_NAME_NOT_NULL);
        }
        $this->from = $tableName;
        return $this;
    }

    /**
     * 设置[where]条件
     *
     * @param string|array $where
     * @return $this 失败抛出异常
     * @throws BaseException 异常
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
     * @throws BaseException 异常
     */
    public function andWhere($where)
    {
        $whereStr = $this->parseWhere($where);
        if ($whereStr) {
            $this->where = $this->where ? $this->where.' and '.$whereStr : $whereStr;
        }
        return $this;
    }

    /**
     * 添加or[where]条件
     *
     * @param string|array $where 条件
     * @return $this 失败抛出异常
     * @throws BaseException 异常
     */
    public function orWhere($where)
    {
        $whereStr = $this->parseWhere($where);
        if ($whereStr) {
            $this->where = $this->where ? $this->where.' or '.$whereStr : $whereStr;
        }
        return $this;
    }

    /**
     * 解析where
     * @param string|array $where 条件
     * @return string 成功返回字符串
     * @throws BaseException 失败抛出异常
     */
    protected function parseWhere($where)
    {
        if ($where === null || $where === "" || $where === []) {
            return '';
        }
        switch (true) {
            case is_string($where)://where字符串直接返回
                return $where;
                break;
            case is_array($where):
                $whereStr = '';
                foreach ($where as $key => $row) {
                    $rowLen = is_array($row) ? count($row):0;
                    $on     = isset($row['on']) && $row['on']==self::ON_OR ? self::ON_OR: self::ON_AND;
                    $op     = isset($row['op']) && $row['op'] ? $row['op']: '=';
                    $value  = null;
                    switch (true) {
                        case $rowLen > 0:
                            $value  = isset($row['rawValue']) ? $row['rawValue']: null;
                            break;
                        case is_string($row) || is_numeric($row):
                            $value = $row;
                            break;
                    }
                    if ($value === null) {
                        continue;
                    }
                    $value      = isset($row['rawValue']) ? $value : '\''.addslashes($value).'\'';
                    $tmpWhere   = implode(" ", [$key, $op, $value]);
                    $whereStr   .= empty($whereArr) ? $tmpWhere : " $on ".$tmpWhere;
                }
                return $whereStr;
                break;
            default:
                throw new BaseException('', BaseException::WHERE_NOT_PARSE);
                break;
        }
    }

    public function findOne()
    {
        $fieldStr = $this->select ? implode(',', $this->select) : '*';
        $sql = "select $fieldStr"
    }
    /**
     * 根据sql查询数据(返回一行记录)
     *
     *
     * @param string $sql 原始sql
     * @param array $params 参数
     * @param array $types 参数类型
     *
     * @see \Doctrine\DBAL\Connection::fetchAssoc()
     * @return mixed|array
     */
    public function findOneBySql($sql, array $params = array(), array $types = array())
    {
        return $this->DB->fetchAssoc($sql, $params, $types);
    }

    /**
     * 根据sql查询数据(返回多行行记录)
     *
     * @param string $sql 原始sql
     * @param array $params 参数
     * @param array $types 参数类型
     *
     * @see \Doctrine\DBAL\Connection::fetchAll()
     * @return mixed|array
     */
    public function findAllBySql($sql, array $params = array(), array $types = array())
    {
        return $this->DB->fetchAll($sql, $params, $types);
    }
}