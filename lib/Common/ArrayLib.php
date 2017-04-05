<?php
namespace Wei\Base\Common;

/**
 * Class ArrayLib
 * @package Wei\Base\Common
 */
class ArrayLib
{
    /**
     * 随机从数组中取出一组值
     * no safe
     * @param array $array 数组
     * @param integer $number 随机值数量
     * @return array 
     */
    public static function array_rand_values($array, $number)
    {
        if (!is_array($array)) {
            return array();
        }
        $array_keys = array_rand($array,$number);
        is_array($array_keys) ? null : $array_keys = array($array_keys);
        $index = 0;
        $arr_temp = array();
        foreach ( $array as $key => $row) {
            if (in_array($key, $array_keys,true)) {
                $arr_temp[$key] = $row;
                $index++;
            }
            if ($index >= $number) {
                unset($array);
                break;
            }
        }
        return $arr_temp;
    }
    
    /**
     * 数组转key
     * @param array $array
     * @return string
     */
    public static function array_to_key($array)
    {
        $key = md5(serialize( $array ));
        return $key;
    }
    
    /**
     * 二维数组子数组属性变为建名
     * @param array $array
     * @param string $row_field_name
     * @return array
     */
    public static function array_row_field_to_key($array, $row_field_name )
    {
        if (!is_array($array)) {
            return array();
        }
        $temp_data = array();
        foreach($array as $key => $row ){
            if (isset($row[$row_field_name])) {
                $temp_data[$row[$row_field_name]] = $row;
            }
            unset($array[$key]);
        }
        return $temp_data;
    }
    
    /**
     * 数组分页
     * demo:
     *
     * @param array $array
     * @param integer $page
     * @param integer $pageSize
     * @return array
     */
    public static function array_page($array, $page=1, $pageSize=10)
    {
        if (!is_array($array)) {
            return array();
        }
        //总数量
        $totalCount = count($array);
        $page       = max(intval($page), 1);
        $pageSize   = max(intval($pageSize), 0);
        $start      = ($page-1) * $pageSize;
        $tmp        = array_slice($array, $start, $pageSize);
        $tmp ? $tmp : $tmp=array();
        return $tmp;
    }
    
    /**
     * 数组转树形
     * @param array $array 数组或者数组对象
     * @param string $sub_field_name 子节点字段名
     * @param string $parent_field_name 父节点字段名
     * @param mixed $parent_value 父节点值
     * @param string $sub_items_name 子树名
     * @return array
     */
    public static function array_to_tree($array, $sub_field_name, $parent_field_name, $parent_value, $sub_items_name)
    {
        if (!is_array($array)) {
            return array();
        }
        $data = array();
        foreach ($array as $key => $row) {
    
            if (isset($row[$parent_field_name])) {
                if ($row[$parent_field_name] == $parent_value) {
                    unset($array[$key]);
                    $row[$sub_items_name] = self::array_to_tree($array, $sub_field_name, $parent_field_name, $row[$sub_field_name], $sub_items_name);
                    $data[] = $row;
                }
            }elseif (isset($row->$parent_field_name)) {
                if ($row->$parent_field_name == $parent_value) {
                    unset($array[$key]);
                    $row->$sub_items_name = self::array_to_tree($array, $sub_field_name, $parent_field_name, $row->$sub_field_name, $sub_items_name);
                    $data[] = $row;
                }
            }
        }
        return $data;
    }
    
    /**
     * 多个键是否全部都在数组中
     * @param array $keys 键
     * @param array $array 数组
     * @return boolean keys存在数组中,返回true,否者false
     */
    public static function array_keys_exists($keys, $array )
    {
        //键不是数组直接返回false
        if (!is_array($keys)) {
            return false;
        }
        foreach ($keys as $k) {
            if (!isset($array[$k])) {
                return false;
            }
        }
        return true;
    }
    
}















