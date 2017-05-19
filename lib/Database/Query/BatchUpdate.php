<?php
namespace Wei\BriefDB\Database\Query;

use Wei\BriefDB\Database\Query\Condition;

/**
 * 批量更新
 * @package Wei\BriefDB\DB
 */
class BatchUpdate
{
    protected $_rawData = [];

    /**
     * 设置批量更新数据
     *
     * @param \Wei\BriefDB\Database\Query\Condition $condition 条件
     * @param array $data 键值数组
     */
    public function setData(Condition $condition, $data)
    {
        $this->clearData();
        $this->addData($condition, $data);
    }
    /**
     * 添加批量更新数据
     *
     * @param \Wei\BriefDB\Database\Query\Condition $condition 条件
     * @param array $data 键值数组
     */
    public function addData(Condition $condition, $data)
    {
        $this->_rawData[] = [
            'condition' => $condition,
            'saveData'  => $data
        ];
    }

    /**
     * 获取批量更新数据
     *
     * @return array
     */
    public function getData()
    {
        return $this->_rawData;
    }

    /**
     * 清空数据
     */
    public function clearData()
    {
        $this->_rawData = [];
    }
}

