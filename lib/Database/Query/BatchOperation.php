<?php
namespace Wei\Base\Database\Query;

/**
 * 批量操作
 *
 * Class BatchOperation
 * @package Wei\Base\DB
 */
class BatchOperation
{
    protected $_rawData = [];

    /**
     * 设置批量更新数据
     *
     * @param mixed $where 条件
     * @param array $data 键值数组
     */
    public function setData($where, $data)
    {
        $this->clearData();
        $this->addData($where, $data);
    }
    /**
     * 添加批量更新数据
     *
     * @param mixed $where 条件
     * @param array $data 键值数组
     */
    public function addData($where, $data)
    {
        $this->_rawData[] = [
            'where' => $where,
            'data'  => $data
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

