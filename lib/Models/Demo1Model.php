<?php
namespace Wei\Base\Models;

use Wei\Base\DB\Model\ModelBase;

/**
 * 测试模型1
 *
 * Class Demo1Model
 * @package Wei\Base\Models
 */
class Demo1Model extends ModelBase
{
    protected $tableName = 'demo1';
    public function primaryKeyName()
    {
        return 'did';
    }

}