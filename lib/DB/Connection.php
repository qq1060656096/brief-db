<?php
namespace Wei\Base\DB;

use Doctrine\DBAL\DriverManager;
use Wei\Base\Config\Config;

/**
 * 数据库连接类
 *
 * @author zhao wei jie
 * @email 1060656096@qq.com
 * @date 2017-03-31
 *
 * Class Connection
 * @package Wei\Base
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
        $db_user = Config::get('db_user', 'db.php');
        $db_pass = Config::get('db_pass', 'db.php');
        $db_host = Config::get('db_host', 'db.php');
        $db_port = Config::get('db_port', 'db.php');
        $db_name = Config::get('db_name', 'db.php');
        //主从配置
        $config = array(
            'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
            'driver' => 'pdo_mysql',
            'master' => array(
                'user'      => $db_user,
                'password'  => $db_pass,
                'host'      => $db_host,
                'port'      => $db_port,
                'dbname'    => $db_name
            ),
            'slaves' => array(
                array(
                    'user'      => $db_user,
                    'password'  => $db_pass,
                    'host'      => $db_host,
                    'port'      => $db_port,
                    'dbname'    => $db_name
                ),
            ),
            'keepSlave'=>true,//注意保持从数据库连接
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