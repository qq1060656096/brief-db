<?php
namespace Wei\BriefDB\Database\Driver\mysql;

use Drupal\Core\Database\Database;
use Wei\BriefDB\Common\ArrayLib;
use Wei\BriefDB\Database\Query\Query;

/**
 * mysql 插入
 *
 * @package Wei\BriefDB\Database\Driver\mysql
 */
class Insert extends \Wei\BriefDB\Database\Query\Insert
{


    /**
     * 参数
     *
     * @var array
     */
    protected $arguments = array();

    /**
     * 插入string
     * @var array
     */
    protected $insertString = null;

    /**
     * 插入值片段
     * @var array
     */
    protected $insertFragment = null;


    /**
     * 设置插入字段
     *
     * @param array $fields 字段
     * @return $this
     */
    protected function fields(array $fields) {
        if (!empty($fields)) {
            if (!is_numeric(key($fields))) {
                $fields = array_keys($fields);
            }
            $this->insertFields = $fields;
        }
        return $this;
    }

    /**
     * 设置插入值
     *
     * @param array $values 值(单行记录)
     * @return $this
     */
    protected function values(array $values)
    {
        if (is_numeric(key($values))) {
            $this->insertValues[] = $values;
        } else {
            // Reorder the submitted values to match the fields array.
            foreach ($this->insertFields as $key) {
                $insert_values[$key] = $values[$key];
            }
            $this->insertValues[] = array_values($insert_values);
        }
        return $this;
    }

    /**
     * 编译插入数据
     * @param array $data 数组
     * @return array
     */
    protected function compileInsertAllData()
    {
        $placeholders = array_pad([], count($this->insertFields), '?');
        $this->arguments = [];
        foreach ($this->insertValues as $key => $row) {
            $this->arguments = ArrayLib::array_add($this->arguments, $row);
            $this->insertFragment[] = '('.implode(', ', $placeholders).')';
        }
        $inserFields = [];
        foreach ($this->insertFields as $key => $row) {
            $insertKey = "`{$key}`";
            $inserFields[$insertKey] = $row;
        }
        $this->insertString = 'INSERT INTO ' . $this->getFrom() . ' (' . implode(', ', $inserFields) . ') VALUES '.implode(', ', $this->insertFragment);
    }

    /**
     * 清空编译变量
     */
    protected function compileClear()
    {
        $this->insertString = null;
        $this->insertFragment = null;
        $this->arguments = null;
    }

    /**
     * 获取插入ID
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    /**
     * 插入单行数据
     *
     * @param array $rowValue 键值数组
     * @return int
     */
    public function insert(array $rowValue)
    {
        $insertData = [];
        foreach ($rowValue as $key => $row) {
            $insertKey = "`{$key}`";
            $insertData[$insertKey] = $row;
        }
        return $this->connection->insert($this->getFrom(), $insertData);
    }

    /**
     * 插入多行数据
     *
     * @param array $rowsValue 多行记录值
     * @return int
     */
    public function insertAll(array $rowsValue)
    {
        $fields = current($rowsValue);
        $this->fields($fields);
        $this->insertValues = null;
        foreach ($rowsValue as $row) {
            $this->values($row);
        }
        $this->compileClear();
        $this->compileInsertAllData();
        return $this->connection->executeUpdate($this->insertString, $this->arguments);
    }
}
