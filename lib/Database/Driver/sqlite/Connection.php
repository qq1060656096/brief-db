<?php
namespace Wei\Base\Database\Driver\sqlite;

use Doctrine\DBAL\DriverManager;
use Wei\Base\Config\Config;
use Wei\Base\Database\Query\ConnectionFactor;

/**
 * sqlite数据库连接类
 *
 * Class Connection
 * @package Wei\Base\Database\Driver\sqlite
 */
class Connection
{
    /**
     * 数据库链接
     * @var \Doctrine\DBAL\Connections\MasterSlaveConnection
     */
    private $dbConnection = null;

    /**
     * 私有构造方法防止初始化
     */
    private function __construct()
    {
        $config     = Config::get(ConnectionFactor::getCurrentConnectionName(), 'db.php');
        //主从配置
        $config = array(
            'driver' => 'pdo_sqlite',
            'user'      => $config['user'],// 账户
            'password'  => $config['password'],// 密码
            'path'      => $config['path'],// sqlite数据库路径
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
     * @return \Doctrine\DBAL\Connections\MasterSlaveConnection
     */
    public static function getInstance()
    {
        static $db = null;
        if(!$db){
            $db = new Connection();
        }
        return $db->dbConnection;
    }
}