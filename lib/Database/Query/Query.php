<?php
namespace Wei\Base\Database\Query;
/**
 * 查询抽象类
 *
 * Class Query
 * @package Drupal\Core\Database\Query
 */
abstract class Query
{

    protected $table = null;

    /**
     * 数据库连接
     *
     * @var \Doctrine\DBAL\Connections\MasterSlaveConnection
     */
    protected $connection;

    /**
     * 初始化
     * @param \Doctrine\DBAL\Connections\MasterSlaveConnection $connection
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
}
