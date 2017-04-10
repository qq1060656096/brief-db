<?php
namespace Wei\Base\Common;

/**
 * 获取类命名空间
 *
 * Class NameSpaceLib
 * @package Wei\Base\Common
 */
class NameSpaceLib
{
    /**
     * 获取命名空间中第一个命名空间名
     * @param string $class_name 类名(实例Exception::class)
     * @return string 第一个命名空间
     */
    public function getFirstNameSpace($class_name)
    {
        $class          = new \ReflectionClass($class_name);
        //获取命名空间
        $namespace      = $class->getNamespaceName();
        //分割获取命名空间
        $arr_namespace  = $namespace ? explode('\\', $namespace) : array("");
        //获取第一命名空间
        $first_namespace= array_shift($arr_namespace);
        return $first_namespace;
    }


    /**
     * 获取命名空间中最后一个命名空间
     * @param string $class_name
     * @return string 最后一个命名空间
     */
    public function getLastNameSpace($class_name)
    {
        $class          = new \ReflectionClass($class_name);
        //获取命名空间
        $namespace      = $class->getNamespaceName();
        //分割获取命名空间
        $arr_namespace  = $namespace ? explode('\\', $namespace):array("");
        //获取最后一个命名空间
        $end_namespace  = array_pop($arr_namespace);
        return $end_namespace;
    }
}