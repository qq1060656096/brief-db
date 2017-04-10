<?php
namespace Wei\Base\DB\Model;


use Wei\Base\Config\Config;
use Wei\Base\DB\Query;

/**
 * 模型抽象类
 *
 * @author zhaosigui
 * @since 2017-03-30
 *
 * Class ModelAbstract
 * @package BaodBase\Model
 */
abstract class ModelBase extends Query
{
    /**
     * @var null|string 表名
     */
    protected $tableName = null;

    /**
     * 初始化
     * @param string|null $tableName 表名
     */
    public function __construct($tableName = null)
    {
        parent::__construct();
        $tableName ? $this->from($tableName) : null;

    }



    /**
     * 获取调用者Mode实例
     *
     * @param bool $isSingle 是不是单列模式，默认true
     * @param bool $debug 调试,默认false
     * @return $this
     */
    public static function getModel($isSingle = true, $debug = false)
    {
        static $instance = null;
        $class = get_called_class();

        switch (true) {
            case $debug === true:
                return $instance;
                break;
            case $isSingle === true && isset($instance[$class]):
                break;
            default:
                $instance[$class] = new $class();
                $instance[$class]->from($instance[$class]->getRealTableName());
                break;
        }
        return $instance[$class];
    }

    /**
     * 表名(不带前缀)
     *
     * @author zhaosigui
     * @since 2017-03-22
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * 获取表前缀
     * @return string
     */
    public function getTablePrefix(){
        return Config::get('table_prefix', 'db.php');
    }
    /**
     * 获取真实表名(完整表名)
     *
     * @return string
     */
    public function getRealTableName()
    {
        $realTableName = $this->getTablePrefix().$this->getTableName();
        return $realTableName;
    }

    /**
     * 主键字段名
     *
     * @author zhaosigui
     * @since 2017-03-22
     *
     * @return string
     */
    abstract public function primaryKeyName();

    /**
     * 根据主键查询数据
     *
     * @param string $pk 主键值
     * @return array|mixed
     */
    public function findOneByPk($pk)
    {
        $query      = $this;
        $primaryKey = $this->primaryKeyName();
        $where      = ["$primaryKey" => $pk];
        return $query->where($where)->findOne();
    }
}