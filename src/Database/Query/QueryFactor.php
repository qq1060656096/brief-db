<?php
namespace Zwei\BriefDB\Database\Query;

use Doctrine\DBAL\Connection;

/**
 * 查询工厂类
 *
 * Class QueryFactor
 * @package Zwei\BriefDB\Database\Query
 */
class QueryFactor
{

    /**
     * 获取Delete实例
     *
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $driver 驱动名
     * @return \Zwei\BriefDB\Database\Driver\mysql\Delete
     */
    public static function getDelete(Connection $connection, $driver = '')
    {
        $class = "\\Zwei\\BriefDB\\Database\\Driver\\{$driver}\\Delete";
        return new $class($connection);
    }

    /**
     * 获取Insert实例
     *
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $driver 驱动名
     * @return \Zwei\BriefDB\Database\Driver\mysql\Insert
     */
    public static function getInsert(Connection $connection, $driver = '')
    {
        $class = "\\Zwei\\BriefDB\\Database\\Driver\\{$driver}\\Insert";
        return new $class($connection);
    }

    /**
     * 获取Update实例
     *
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $driver 驱动名
     * @return \Zwei\BriefDB\Database\Driver\mysql\Update
     */
    public static function getUpdate(Connection $connection, $driver = '')
    {
        $class = "\\Zwei\\BriefDB\\Database\\Driver\\{$driver}\\Update";
        return new $class($connection);
    }
    /**
     * 获取Select实例
     *
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $driver 驱动名
     * @return \Zwei\BriefDB\Database\Driver\mysql\Select
     */
    public static function getSelect(Connection $connection, $driver = '')
    {
        $class = "\\Zwei\\BriefDB\\Database\\Driver\\{$driver}\\Select";
        return new $class($connection);
    }
}