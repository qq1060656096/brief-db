<?php
namespace Wei\Base\Database\Query;

use Doctrine\DBAL\Connection;
use Wei\Base\Exception\BaseException;

/**
 * 删除
 *
 * @package Wei\Base\Database\Query
 */
class Delete extends Query
{

    /**
     * 删除
     *
     * @return int
     * @throws BaseException
     */
    public function delete()
    {

        //没有设置条件不能删除
        if ($this->condition->count() < 1) {
            throw new BaseException();
        }
        $whereStr       = (string)$this->condition->compile();
        $whereArguments = $this->condition->arguments();
        $sql            = "DELETE FROM {$this->table} WHERE ".$whereStr;
        return $this->connection->executeUpdate($sql, $whereArguments);
    }
}
