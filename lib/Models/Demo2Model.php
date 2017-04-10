<?php
namespace Wei\Base\Models;

use Wei\Base\DB\Model\ModelBase;

/**
 * 测试模型2
 *
 * Class Demo2Model
 * @package Wei\Base\Models
 */
class Demo2Model extends ModelBase
{
    protected $tableName = 'demo2';

    public function primaryKeyName()
    {
        return 'did';
    }

}