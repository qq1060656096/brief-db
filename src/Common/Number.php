<?php

namespace Zwei\BriefDB\Common;

/**
 * Class Number
 * @package Zwei\BriefDB\Common
 */
class Number
{
    /**
     * 保留数字小数位数(不会四舍五入)
     * @param number $number 数字
     * @param int $digit 保留小数位数,默认2位
     * @return bool|float 成功返回float,失败返回false[注意参数错误也会返回false]
     */
    public function keep_decimals($number, $digit = 2)
    {
        //不是数字
        if (!is_numeric($number)) {
            return false;
        }
        //保留小数位数必须是数组,并且不能小于0
        if (!is_numeric($digit) || $digit < 0) {
            return false;
        }
        //以小数点分割
        $arr = explode('.', $number);
        //整数部分
        $integer = $arr[0];
        //小数部分
        $decimals = isset($arr[1]) ? "{$arr[1]}" : '';
        //小数位数
        $decimalsLen = strlen($decimals);
        //保留的小数部分
        $decimalsKeep = '';
        //小数位数>=保留小数位数
        if ($decimalsLen >= $digit) {
            $decimalsKeep = substr($decimals, 0, $digit);
        } else {
            $decimalsKeep = str_repeat('0', $digit - $decimalsLen);
            $decimalsKeep = "{$decimals}$decimalsKeep";
        }
        $keep_arr = array(
            $integer,
            $decimalsKeep
        );
        $numberKeep = implode('.', $keep_arr);
        //删除右边的小数点"."
        $numberKeep = rtrim($numberKeep, '.');
        return $numberKeep;
    }

    /**
     * 保留数字小数位数(不会四舍五入)
     * @param number $number 数字
     * @param int $digit 保留小数位数,默认2位
     * @return bool|float 成功返回float,失败返回false[注意参数错误也会返回false]
     */
    public function keep_decimals_new($number, $digit = 2)
    {
        //不是数字
        if (!is_numeric($number)) {
            return false;
        }
        //保留小数位数必须是数组,并且不能小于0
        if (!is_numeric($digit) || $digit < 0) {
            return false;
        }
        $str_len = strlen("{$number}");
        $pos = strrpos($number, '.');
        $pos = $pos === false ? 0 : ++$pos;
        $new_len = $pos + $digit;
        //如果$number长度>=保留小数长度
        if ($str_len >= $new_len) {
            $str = substr($number, 0, $pos + $digit);
        } else {//$number长度<保留小数长度,直接填充0
            $tmp = str_repeat('0', $new_len - $str_len);
            $str = "{$number}{$tmp}";
        }
        //删除右边的小数点"."
        $numberKeep = rtrim($str, '.');
        return $numberKeep;
    }
}
