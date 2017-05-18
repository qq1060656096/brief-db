<?php
namespace Wei\Base\Database\Query;

use Doctrine\DBAL\Connection;
use Wei\Base\Exception\BaseException;

/**
 * 删除
 *
 * @package Wei\Base\Database\Query
 */
abstract class Delete extends Query
{
    /**
     * 初始化
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $table 表名
     */
    public function __construct(Connection $connection, $table = '')
    {
        parent::__construct($connection);
        $this->table = $table;
        $this->condition = new Condition('AND');
    }
    /**
     * 删除
     *
     * @return int
     */
    public abstract function delete();
}
