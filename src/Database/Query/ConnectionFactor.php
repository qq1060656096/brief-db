<?php
namespace Wei\BriefDB\Database\Query;


use Wei\BriefDB\Config\Config;
use Doctrine\DBAL\Connection;

/**
 * 数据库连接工厂类
 *
 * Class ConnectionFactor
 * @package Wei\BriefDB\Database\Query
 */
class ConnectionFactor
{
    /**
     * 数组连接实例
     * @var array
     */
    protected static $connectionDataInstance = null;

    /**
     * 活动连接名
     * @var null
     */
    protected static $activeName = null;

    /**
     * 默认连接名
     */
    const DEFAULT_CONNECT_NAME = 'default';


    /**
     * 获取指定连接表前缀
     *
     * @param string $connectName 连接名
     * @return string
     */
    public static function getConnectTablePrefix($connectName)
    {
        $config     = Config::get($connectName, 'db.php');
        return isset($config['table_prefix']) ? $config['table_prefix'] : '';
    }

    /**
     * 获取连接信息
     *
     * @param string $connectName
     * @return ConnectionData
     */
    public static function getConnectionData($connectName)
    {
        //如果设置连接数据,直接返回
        if (isset(self::$connectionDataInstance[$connectName])) {
            return  self::$connectionDataInstance[$connectName];
        }

        //根据连接名获取配置信息
        $config         = Config::get($connectName , 'db.php');
        $driver         = $config['driver'];
        //根据驱动获取数据库连接信息
        $driver_class   = "\\Wei\\BriefDB\\Database\\Driver\\{$driver}\\Connection";
        /* @var $connection \Doctrine\DBAL\Connection */
        $connection     = $driver_class::getInstance($connectName);
        $connectionData = new ConnectionData();
        $connectionData->setData($connectName, $driver, $connection);
        return self::$connectionDataInstance[$connectName] = $connectionData;
    }

    /**
     * 获取数据库连接
     *
     * @param string $connection 连接名
     * @param bool $isActive 是否设置为活动连接
     * @return \Doctrine\DBAL\Connection
     */
    public static function getInstance($connectName = self::DEFAULT_CONNECT_NAME, $isActive = false)
    {
        self::getActiveConnectName($connectName, $isActive);
        $connectionData = self::getConnectionData($connectName);
        return $connectionData->getConnection();
    }

    /**
     * 获取设置连接名
     *
     * @param string $connectName 连接名
     * @param bool $isActive 是否设置为活动连接
     */
    public static function getActiveConnectName($connectName = '', $isActive = false)
    {
        switch (true) {
            // 强制设置活动名
            case $isActive:
                self::$activeName = $connectName;
                break;
            // 已经设置过活动连接了
            case isset(self::$activeName):
                break;
            // 没有设置活动连接并且传递了$connectName,就设置连接名
            case $connectName:
                self::$activeName = $connectName;
                break;
            // 默认活动连接名
            default:
                self::$activeName = self::DEFAULT_CONNECT_NAME;
                break;
        }
        return self::$activeName;
    }

    /**
     * 获取连接驱动名
     *
     * @param string $connectName 连接名
     * @return string
     */
    public static function getConnectionDriver($connectName)
    {
        return self::getConnectionData($connectName)->getDriver();
    }

    /**
     * 开启sql日志
     *
     * @param string $connectName 连接名
     * @see Query::getLastRawSql()
     * @return
     */
    public static function enabledSqlLog($connectName = '')
    {

        if ($connectName && $connectionData = self::getConnectionData($connectName)) {

        }// 获取活动链接名的数据库连接信息
        else if($connectionData = self::getConnectionData(self::getActiveConnectName($connectName))) {

        } else {
            return null;
        }

        if (empty($connectionData->getLogger())) {
            $connectionData->setLogger(new \Doctrine\DBAL\Logging\DebugStack());
        }
    }

    /**
     * 获取最后执行sql
     *
     * @param string $connectName 连接名
     * @see Query::enabledSqlLog()
     * @return array|null
     */
    public static function getLastRawSql($connectName = '')
    {
        if ($connectName && $connectionData = self::getConnectionData($connectName)) {

        }// 获取活动链接名的数据库连接信息
        else if($connectionData = self::getConnectionData(self::getActiveConnectName($connectName))) {

        } else {
            return null;
        }


        if(!isset($connectionData->getLogger()->queries[$connectionData->getLogger()->currentQuery])){
            $rawSql['msg']           = 'queries 没有数据';
            return $rawSql;
        }

        $rawSql = $connectionData->getLogger()->queries[$connectionData->getLogger()->currentQuery];
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

}