<?php
namespace Wei\Base\Database\Query;
use Wei\Base\Config\Config;


/**
 * 数据库连接工厂类
 *
 * Class ConnectionFactor
 * @package Wei\Base\Database\Query
 */
class ConnectionFactor
{
    /**
     * 最后使用驱动
     *
     * @var null
     */
    protected static $driver = null;

    /**
     * 最后使用驱动
     *
     * @var null
     */
    protected static $connectionName = null;

    /**
     * 存放数据库驱动连接
     * @var null
     */
    protected static $connection = null;

    /**
     * 数据库调试信息
     * @var null
     */
    protected static $logger = null;
    /**
     * 获取数据库连接
     * @param string $connection 连接名
     * @return \Doctrine\DBAL\Connection
     */
    public static function getInstance($connectionName = 'default')
    {
        self::$connectionName = $connectionName;
        $config = Config::get(self::getConnectionName() , 'db.php');
        if(isset(self::$connection[self::$connectionName]) && self::$connection[self::$connectionName] !== null){
            return self::$connection[self::$connectionName];
        }

        switch ($config['driver']) {
            case 'mysql':
                self::$connection[self::$connectionName] = \Wei\Base\Database\Driver\mysql\Connection::getInstance();
                break;
        }
        return self::$connection[self::$connectionName];
    }

    /**
     * 获取连接名
     *
     * @return null|string
     */
    public static function getConnectionName()
    {
        self::$connectionName = !empty(self::$connectionName ) ? self::$connectionName : 'default';
        return self::$connectionName;
    }


    /**
     * 开启sql日志
     *
     * @see Query::getLastRawSql()
     * @return
     */
    public static function enabledSqlLog($connectionName = 'default')
    {
        if (!isset(self::$logger[$connectionName])) {
            self::$logger[$connectionName] = new \Doctrine\DBAL\Logging\DebugStack();
            self::getInstance($connectionName)->getConfiguration()->setSQLLogger(self::$logger[$connectionName] );
        }
        return self::$logger[$connectionName];
    }

    /**
     * 获取最后执行sql
     *
     * @see Query::enabledSqlLog()
     * @return array|null
     */
    public static function getLastRawSql($connectionName = 'default')
    {
        if (!isset(self::$logger[$connectionName])) {
            return null;
        }
        if(!isset(self::$logger[$connectionName]->queries[self::$logger[$connectionName]->currentQuery])){
            $rawSql['msg']           = 'queries 没有数据';
            return $rawSql;
        }

        $rawSql = self::$logger[$connectionName]->queries[self::$logger[$connectionName]->currentQuery];
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