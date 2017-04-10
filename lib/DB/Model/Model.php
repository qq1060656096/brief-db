<?php
namespace Wei\Base\DB\Model;


/**
 * 空模型类
 *
 * Class Model
 * @package Wei\Base\DB\Model
 */
class Model extends ModelBase
{
    public function primaryKeyName()
    {
        return '';
    }

    /**
     * 设置表名
     *
     * @param string $tableName 表名
     * @return $this
     */
    public function setTable($tableName)
    {
        $this->tableName = $tableName;
        $this->from($this->getRealTableName());
        return $this;
    }
}