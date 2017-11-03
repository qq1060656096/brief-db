<?php
namespace Wei\BriefDB\Database\Driver\mysql;

use Doctrine\DBAL\DriverManager;
use Wei\BriefDB\Config\Config;
use Wei\BriefDB\Database\Query\ConnectionFactor;

/**
 * mysql数据库连接类
 *
 * @author zhao wei jie
 * @email 1060656096@qq.com
 * @date 2017-03-31
 *
 * Class Connection
 * @package Wei\BriefDB\Database\Driver\mysql
 */
class Connection
{


    /**
     * 私有构造方法防止初始化
     * @param string $connectName 连接名
     */
    private function __construct($connectName)
    {
        $config     = Config::get($connectName, 'db.php');
        //主从配置
        $config = array(
            'driver' => 'pdo_mysql',
            'user'      => $config['user'],
            'password'  => $config['password'],
            'host'      => $config['host'],
            'port'      => $config['port'],
            'dbname'    => $config['dbname']
        );

        $conn = DriverManager::getConnection($config);
        $this->dbConnection = $conn;
    }

    /**
     * 防止克隆
     */
    private function __clone()
    {
    }

    /**
     * 获取数据库连接实例
     *
     * @param string $connectName 连接名
     * @return \Doctrine\DBAL\Connections\MasterSlaveConnection
     */
    public static function getInstance($connectName)
    {
        static $db = null;
        if(!$db){
            $db = new Connection($connectName);
        }
        return $db->dbConnection;
    }
}