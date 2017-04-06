<?php
namespace Wei\Base\Exception;


use Throwable;

class QueryException extends BaseException
{
    /**
     * where 无法解析
     */
    const WHERE_NOT_PARSE = 41001;

    /**
     * select 无法解析
     */
    const SELECT_NOT_PARSE = 41002;

    /**
     * group by 无法解析
     */
    const GROUP_BY_NOT_PARSE = 41003;

    /**
     * order by 无法解析
     */
    const ORDER_BY_NOT_PARSE = 41004;

    /**
     * 表名不能为空
     */
    const TABLE_NAME_NOT_NULL = 41009;
    /**
     * 参数非法
     */
    const PARAMS_ILLEGAL = 41010;

    /**
     * 多行更新参数错误
     */
    const UPDATE_ALL_PARAMS_ILLEGAL = 41011;

}