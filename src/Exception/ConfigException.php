<?php
namespace Wei\BriefDB\Exception;


class ConfigException extends BaseException
{
    /**
     * 键不存在
     */
    const KEY_NOT_FOUND = 41301;

    /**
     * 文件不存在
     */
    const FILE_NOT_FOUND = 41302;

}