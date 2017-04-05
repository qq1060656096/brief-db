<?php
namespace Wei\Base\DB;

use Doctrine\DBAL\DriverManager;

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
        $conn = DriverManager::getConnection(
            array(
                'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
                'driver' => 'pdo_mysql',
                'master' => array(
                    'user' => 'root',
                    'password' => 'root',
                    'host' => 'localhost',
                    'dbname' => 'test'
                ),
                'slaves' => array(
                    array(
                        'user' => 'root',
                        'password'=>'root',
                        'host' => 'localhost',
                        'dbname' => 'test'
                    ),
                ),
                'keepSlave'=>true,//注意保持从数据库连接
            )
        );
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