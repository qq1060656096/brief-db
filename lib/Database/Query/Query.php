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

}
