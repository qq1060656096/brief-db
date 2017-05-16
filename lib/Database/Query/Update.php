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
class Update extends Query
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
     * 编译data数据
     * @param array $data 数组
     * @return array
     */
    public function compileData($data)
    {
        $setFragment    = [];
        $arguments      = [];
        foreach ($data as $key => $value) {
            $operator   = '=';
            if (isset($value['operator'])) {
                $operator = $value['operator'];
                unset($value['operator']);
            }
            $field      = $key;
            if (isset($value['field'])) {
                $field = $value['field'];
                unset($value['field']);
            }
            $value      = $value;
            if (isset($value['value'])) {
                $value = $value['value'];
                unset($value['value']);
            }
            //设置自定义set
            if (!isset($value['setRaw'])) {
                $setFragment[]  = "{$field} {$operator} ?";
                $arguments[]    = $value;
            } else {
                $setFragment[]  = "{$field} {$operator} {$value['setRaw']}";
                is_array($value) ?null : $value = [$value];
                $arguments = ArrayLib::array_add($arguments, $value);
            }

        }
        $this->setString = implode(',', $setFragment);
        $this->arguments = $arguments;
        return [$setFragment, $arguments];
    }


    /**
     * 更新数据
     *
     * @param array $data 数据
     * @return 成功返回受影响行数,否者失败
     */
    public function update(array $data)
    {
        $this->compileData($data);
        $arguments      = $this->arguments;
        $sql            = 'UPDATE '.$this->table.' SET '.$this->setString;
        //设置了条件
        if ($this->condition->count() > 0) {
            $whereStr       = (string)$this->condition->compile();
            $whereArguments = $this->condition->arguments();
            $sql            = $sql.' where '.$whereStr;
            $arguments      = ArrayLib::array_add($arguments, $whereArguments);
        }
        return $this->connection->executeQuery($sql, $arguments);
    }

    /**
     * 批量更新
     *
     * @param BatchUpdate $batchUpdate 批量更新类
     * @param bool $strict 严格模式(默认false,如果是true严格模式,必须全部保存都有受影响行数才会保存成功)
     * @return bool|int
     */
    public function updateAll(BatchUpdate $batchUpdate, $strict = false)
    {
        $data       = $batchUpdate->getData();
        $count      = count($data);
        $saveCount  = 0;
        $this->connection->beginTransaction();
        foreach ($data as $key => $row) {
            /* @var $condition \Wei\Base\Database\Query\Update */
            list($condition, $saveData) = $row;
            $obj = new Update($this->connection, $this->table);
            $obj->conditionComplex((string)$condition, $condition->arguments());
            $result = $obj->update($saveData);
            $result ? $saveCount++ : null;

        }

        switch (true) {
            // 普通模式
            case $strict === false:
                $this->connection->commit();
                break;
            // 严格模式必须全部有更新才会保存
            case $saveCount == $count:
                $this->connection->commit();
                break;
            // 严格模式保存失败
            default:
                $this->connection->rollBack();
                return false;
                break;
        }
        return $saveCount;
    }
}
