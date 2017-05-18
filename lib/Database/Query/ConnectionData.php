<?php
namespace Wei\Base\Database\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\SQLLogger;

/**
 * 数据库连接信息类
 *
 * Class ConnectionData
 * @package Wei\Base\Database\Query
 */
class ConnectionData
{
    /**
     * 连接名
     *
     * @var string
     */
    protected $connectionName = null;

    /**
     * 驱动名
     * @var string
     */
    protected $driver = null;

    /**
     * 数据库连接
     * @var \Doctrine\DBAL\Connection|null
     */
    protected $connection = null;
    /**
     * sql日志
     *
     * @var \Doctrine\DBAL\Logging\SQLLogger|null
     */
    protected $logger = null;

    /**
     * 初始化
     * @param string $connectionName 连接名
     * @param string $driver 驱动名
     * @param Connection $connection 数据库连接
     * @return $this
     */
    public function setData($connectionName, $driver,Connection $connection)
    {
        $this->connectionName = $connectionName;
        $this->driver = $driver;
        $this->connection = $connection;
        return $this;
    }

    /**
     * 获取连接名称
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * 获取驱动名称
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * 获取数据库连接
     *
     * @return Connection|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * 获取SQLLogger
     *
     * @return SQLLogger|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * 设置sql日志
     *
     * @param SQLLogger|null $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        $this->connection->getConfiguration()->setSQLLogger($this->logger);
        return $this;
    }

}