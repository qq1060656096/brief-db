<?php
namespace Wei\Base\Database\Query;


use Wei\Base\Config\Config;
use Doctrine\DBAL\Connection;

/**
 * 数据库连接工厂类
 *
 * Class ConnectionFactor
 * @package Wei\Base\Database\Query
 */
class ConnectionFactor
{
    /**
     * 数组连接实例
     * @var array
     */
    protected static $connectionDataInstance = null;
    /**
     * 当前连接名
     * @var null
     */
    protected static $currentConnectionName = null;
    /**
     * 获取连接信息
     *
     * @param string $connectionName
     * @return ConnectionData
     */
    protected static function getConnectionData($connectionName)
    {
        //如果设置连接数据,直接返回
        if (isset(self::$connectionDataInstance[$connectionName])) {
            return  self::$connectionDataInstance[$connectionName];
        }
        //根据连接名获取配置信息
        $config         = Config::get($connectionName , 'db.php');
        $driver         = $config['driver'];
        //根据驱动获取数据库连接信息
        $driver_class   = "\\Wei\\Base\\Database\\Driver\\{$driver}\\Connection";
        /* @var $connection \Doctrine\DBAL\Connection */
        $connection     = $driver_class::getInstance();
        $connectionData = new ConnectionData();
        $connectionData->setData($connectionName, $driver, $connection);
        return self::$connectionDataInstance[$connectionName] = $connectionData;

    }

    /**
     * 获取数据库连接
     * @param string $connection 连接名
     * @return \Doctrine\DBAL\Connection
     */
    public static function getInstance($connectionName = 'default')
    {
        $connectionName = self::getCurrentConnectionName($connectionName);
        $connectionData = self::getConnectionData($connectionName);
        return $connectionData->getConnection();
    }

    /**
     * 获取当前连接名
     *
     * @param string $connectionName 连接名
     * @return string
     */
    public static function getCurrentConnectionName($connectionName = '')
    {
        switch (true) {
            //如果设置了连接名,就用设置的连接名
            case !empty($connectionName):
                self::$currentConnectionName = $connectionName;
                break;
            //如果没有设置连接名,并且已经设置过了当前连接名
            case !empty(self::$currentConnectionName):
                break;
            //没有设置连接名就使用默认的
            default:
                self::$currentConnectionName = 'default';
                break;
        }
        return self::$currentConnectionName;
    }


    /**
     * 开启sql日志
     *
     * @see Query::getLastRawSql()
     * @return
     */
    public static function enabledSqlLog($connectionName = '')
    {
        $connectionName = self::getCurrentConnectionName($connectionName);
        $connectionData = self::getConnectionData($connectionName);
        if (empty($connectionData->getLogger())) {
            $connectionData->setLogger(new \Doctrine\DBAL\Logging\DebugStack());
        }
    }

    /**
     * 获取最后执行sql
     *
     * @see Query::enabledSqlLog()
     * @return array|null
     */
    public static function getLastRawSql($connectionName = '')
    {
        $connectionName = self::getCurrentConnectionName($connectionName);
        $connectionData = self::getConnectionData($connectionName);
        if (!$connectionData) {
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