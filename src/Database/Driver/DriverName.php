<?php
namespace Zwei\BriefDB\Database\Driver;
/**
 * 驱动名
 *
 * Class DriverName
 * @package Zwei\BriefDB\Database\Driver
 */
class DriverName
{
    /**
     * mysql驱动
     */
    const MYSQL = 'mysql';

    /**
     * 获取所有驱动
     *
     * @return array
     */
    public static function getDrivers()
    {
        return [
            self::MYSQL,
        ];
    }
}