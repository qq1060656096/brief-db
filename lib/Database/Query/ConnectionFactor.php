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
     * 获取数据库连接
     * @param string $connection 连接名
     * @return \Doctrine\DBAL\Connections\MasterSlaveConnection
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
}