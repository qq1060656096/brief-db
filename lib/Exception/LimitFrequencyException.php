<?php
namespace Wei\Base\Exception;

/**
 * 限制访问频率异常
 *
 * Class LimitFrequencyException
 * @package Wei\Base\Exception
 */
class LimitFrequencyException extends BaseException
{
    /**
     * 键已设置为锁定
     */
    const KEY_IS_LOCK = 41201;

    /**
     * 键不是访问频率限制键
     */
    const KEY_IS_NOT_FREQUENCY_KEY = 41202;
}