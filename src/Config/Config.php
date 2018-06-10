<?php
namespace Zwei\BriefDB\Config;

use function Composer\Autoload\includeFile;
use Zwei\BriefDB\Common\Composer;
use Zwei\BriefDB\Exception\ConfigException;

/**
 * 读取配置
 *
 * Class Config
 * @package Zwei\BriefDB\Config
 */
class Config
{
    /**
     * 默认配置文件名
     */
    const DEFAULT_FILE_NAME = 'config.php';

    /**
     * @var null 默认目录
     */
    protected static $dirPath = null;

    /**
     * 设置配置目录path
     *
     * @param string $dirPath 目录path
     */
    public static function setDir($dirPath)
    {
        self::$dirPath = $dirPath;
    }

    /**
     * 获取配置目录path
     *
     * @return string
     */
    public static function getDirPath()
    {
        $dir = Composer::getComposerVendorDir().'/config';
        self::$dirPath = self::$dirPath === null ? $dir : self::$dirPath;
        self::$dirPath = str_replace('\\', '/', self::$dirPath);
        return self::$dirPath;
    }


    /**
     * 读取配置
     *
     * @param string $name 配置项
     * @param string $fileName 配置文件名 默认"config.php"
     * @param bool $isDynamic 是否是动态配置(默认不是)
     * @param bool $debug 调试
     * @return 值
     * @throws ConfigException 抛出异常
     */

    public static function get($name, $fileName = self::DEFAULT_FILE_NAME, $isDynamic = false, $debug = false)
    {
        static $configFileArr = null;
        static $configData = [];
        if ($debug) {
           return [$configFileArr, $configData];
        }

        $fileName   = str_replace('\\', '/', $fileName);
        $configFile = rtrim( self::getDirPath(), '/').'/'.ltrim($fileName, '/');
        switch (true) {
            case $isDynamic://动态配置
            case !isset($configFileArr[$configFile])://没有设置配置就加载配置文件
                if (!file_exists($configFile)) {
                    throw new ConfigException("$configFile not found", ConfigException::FILE_NOT_FOUND);//配置文件不存在
                }
                $configFileArr[$configFile] = $configFile;
                $config     = include $configFile;
                $configData = array_merge($configData, $config);
                break;

            default:
                break;
        }

        if(!isset($configData[$name])){
            throw new ConfigException('', ConfigException::KEY_NOT_FOUND);
        }
        return $configData[$name];
    }
}