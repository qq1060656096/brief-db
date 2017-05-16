<?php

namespace Wei\Base\Database\Query;


use Wei\Base\Common\ArrayLib;
use Doctrine\DBAL\Connection;

/**
 * 插入
 */
abstract class Insert extends Query
{

    /**
     * @var array|null 插入字段
     */
    protected $insertFields = null;

    /**
     * @var array|null 插入值
     */
    protected $insertValues = null;


    /**
     * 初始化
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param $table 表名
     */
    public function __construct(Connection $connection, $table)
    {
        parent::__construct($connection);
        $this->table = $table;
    }

    /**
     * 获取插入ID
     * @return string
     */
    public abstract function getLastInsertId();

    /**
     * 插入单行数据
     *
     * @param array $rowValue 键值数组
     * @return int
     */
    public abstract function insert(array $rowValue);

    /**
     * 插入多行数据
     *
     * @param array $rowsValue 多行记录值
     * @return int
     */
    public abstract function insertAll(array $rowsValue);
}
