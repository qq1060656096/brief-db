<?php
namespace Wei\BriefDB\Database;

use Wei\BriefDB\Config\Config;
use Wei\BriefDB\Database\Query\ConnectionFactor;
use Wei\BriefDB\Database\Query\QueryFactor;


/**
 * 数据库操作
 * Class Db
 * @package Wei\BriefDB\Database
 */
class Db
{
    /**
     * 获取链接名
     *
     * @return string
     */
    public static function getConnectName()
    {
        return 'default';
    }

    /**
     * 获取表名(带前缀)
     * @param string $tableName
     * @return string
     */
    public static function getRealTable($tableName)
    {
        return Config::get(self::getConnectName(), 'db.php')['table_prefix'].$tableName;
    }


    /**
     * 获取数据库连接
     * @return \Doctrine\DBAL\Connection 数据库连接
     */
    public static function getConnection()
    {
        // 连接名
        $connectName    = self::getConnectName();
        $connection     = ConnectionFactor::getInstance($connectName);
        return $connection;
    }

    /**
     * 获取驱动名
     *
     * @return string
     */
    public static function getDriverName()
    {
        // 连接名
        $connectName    = self::getConnectName();
        $diverName      = ConnectionFactor::getConnectionData($connectName)->getDriver();
        return $diverName;
    }

    /**
     * 插入
     * @return \Wei\BriefDB\Database\Query\Insert
     */
    public static function getInsert()
    {
        $connection = self::getConnection();
        $diverName = self::getDriverName();
        return QueryFactor::getInsert($connection, $diverName);
    }

    /**
     * 删除
     * @return \Wei\BriefDB\Database\Query\Delete
     */
    public static function getDelete()
    {
        $connection = self::getConnection();
        $diverName = self::getDriverName();
        return QueryFactor::getDelete($connection, $diverName);
    }

    /**
     * 更新
     *
     * @return \Wei\BriefDB\Database\Query\Update
     */
    public static function getUpdate()
    {
        $connection = self::getConnection();
        $diverName = self::getDriverName();
        return QueryFactor::getUpdate($connection, $diverName);
    }

    /**
     * 查询
     * @return \Wei\BriefDB\Database\Query\Select
     */
    public static function getSelect()
    {
        $connection = self::getConnection();
        $diverName = self::getDriverName();
        return QueryFactor::getSelect($connection, $diverName);
    }

    /**
     * 获取最后插入id
     *
     * @return string
     */
    public static function lastInsertId()
    {
        $connection = self::getConnection();
        return $connection->lastInsertId();
    }

    /**
     * 启用sql日志
     */
    public static function enabledSqlLog()
    {
        ConnectionFactor::enabledSqlLog(self::getConnectName());
   }

    /**
     * 获取最后执行sql
     * @return array|null
     */
   public static function getLastRawSql()
   {
       return ConnectionFactor::getLastRawSql(self::getConnectName());
   }
}