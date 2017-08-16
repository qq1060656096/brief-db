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
     * @return array [\Doctrine\DBAL\Connection, $diverName驱动名]
     */
    public static function getDbConnection()
    {
        // 连接名
        $connectName    = self::getConnectName();
        $connection     = ConnectionFactor::getInstance($connectName);
        $diverName      = ConnectionFactor::getConnectionData($connectName)->getDriver();
        return [$connection, $diverName];
    }

    /**
     * 插入
     * @return \Wei\BriefDB\Database\Query\Insert
     */
    public static function getInsert()
    {
        list($connection, $diverName)   = self::getDbConnection();
        return QueryFactor::getInsert($connection, $diverName);
    }

    /**
     * 删除
     * @return \Wei\BriefDB\Database\Query\Delete
     */
    public static function getDelete()
    {
        list($connection, $diverName)   = self::getDbConnection();
        return QueryFactor::getDelete($connection, $diverName);
    }

    /**
     * 更新
     *
     * @return \Wei\BriefDB\Database\Query\Update
     */
    public static function getUpdate()
    {
        list($connection, $diverName)   = self::getDbConnection();
        return QueryFactor::getUpdate($connection, $diverName);
    }

    /**
     * 查询
     * @return \Wei\BriefDB\Database\Query\Select
     */
    public static function getSelect()
    {
        list($connection, $diverName)   = self::getDbConnection();
        return QueryFactor::getSelect($connection, $diverName);
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