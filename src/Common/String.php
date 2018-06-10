<?php
namespace Zwei\BriefDB\Common;

/**
 * Class String
 * @package Zwei\BriefDB\Common
 */
class String
{
    /**
     * html转义,安全的html
     * @param array &$array
     */
    public static function str_safe_html(&$array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (!is_array($value)) {
                    //&,",',> ,< 转为html实体 &amp;,&quot;&#039;,&gt;,&lt;
                    $array[$key] = htmlspecialchars($value,ENT_QUOTES);
                } else {
                    self::str_safe_html($array[$key]);
                }
            }
        } else {
            $array = htmlspecialchars($array,ENT_QUOTES);
        }
    }
    /**
     * 字符串模板变量替换
     * @param string $str
     * @param array $array
     * @return string
     */
    public static function str_template_replace($str, $array)
    {
        if (!is_array($array)) {
            return $str;
        }
        foreach ($array as $key => $value) {
            $str = str_replace("{{$key}}",$value,$str);
        }
        return $str;
    } 
}

