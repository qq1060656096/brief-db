<?php
namespace Wei\Base\Database\Query;

use Wei\Base\Common\ArrayLib;
use Doctrine\DBAL\Connection;

/**
 * 更新
 *
 * Class Update
 * @package Wei\Base\Database\Query
 */
abstract class Update extends Query
{


    /**
     * 参数
     *
     * @var array
     */
    protected $arguments = array();

    /**
     * set数组
     * @var array
     */
    protected $setString = null;

    /**
     * 初始化
     * @param \Doctrine\DBAL\Connection $connection 数据库连接
     * @param string $table 表名
     */
    public function __construct(Connection $connection, $table)
    {
        parent::__construct($connection);
        $this->table = $table;
        $this->condition = new Condition('AND');
    }

    /**
     * 更新数据
     *
     * @param array $data 数据
     * @return 成功返回受影响行数,否者失败
     */
    public abstract function update(array $data);

    /**
     * 批量更新
     *
     * @param BatchUpdate $batchUpdate 批量更新类
     * @param bool $strict 严格模式(默认false,如果是true严格模式,必须全部保存都有受影响行数才会保存成功)
     * @return bool|int
     */
    public abstract function updateAll(BatchUpdate $batchUpdate, $strict = false);
}
