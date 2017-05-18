<?php
namespace Wei\Base\Database\Query;

use Doctrine\DBAL\Connection;

/**
 * 查询工厂类
 *
 * Class QueryFactor
 * @package Wei\Base\Database\Query
 */
class QueryFactor
{

    /**
     * 获取Delete实例
     *
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $driver 驱动名
     * @return \Wei\Base\Database\Driver\mysql\Delete
     */
    public static function getDelete(Connection $connection, $driver = '')
    {
        $class = "\\Wei\\Base\\Database\\Driver\\{$driver}\\Delete";
        return new $class($connection);
    }

    /**
     * 获取Insert实例
     *
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $driver 驱动名
     * @return \Wei\Base\Database\Driver\mysql\Insert
     */
    public static function getInsert(Connection $connection, $driver = '')
    {
        $class = "\\Wei\\Base\\Database\\Driver\\{$driver}\\Insert";
        return new $class($connection);
    }

    /**
     * 获取Update实例
     *
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $driver 驱动名
     * @return \Wei\Base\Database\Driver\mysql\Update
     */
    public static function getUpdate(Connection $connection, $driver = '')
    {
        $class = "\\Wei\\Base\\Database\\Driver\\{$driver}\\Update";
        return new $class($connection);
    }
    /**
     * 获取Select实例
     *
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $driver 驱动名
     * @return \Wei\Base\Database\Driver\mysql\Select
     */
    public static function getSelect(Connection $connection, $driver = '')
    {
        $class = "\\Wei\\Base\\Database\\Driver\\{$driver}\\Select";
        return new $class($connection);
    }
}