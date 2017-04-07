<?php
namespace Wei\Base\LimitFrequency;

use Wei\Base\Exception\LimitFrequencyException;

/**
 * 限制访问频率
 *
 * Class LimitFrequency
 * @package Wei\Base\LimitFrequency
 */
class LimitFrequency
{
    /**
     * 锁定值
     */
    const LOCK_VALUE = 'LimitFrequency_lock_value';
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache = null;

    /**
     * 获取缓存
     *
     * @return \Doctrine\Common\Cache\Cache
     * @throws LimitFrequencyException 抛出异常
     */
    public function getCache()
    {
        switch (true) {
            case $this->cache === null:
                throw new LimitFrequencyException('', LimitFrequencyException::CACHE_NOT_NULL);
                break;
            case $this->cache instanceof \Doctrine\Common\Cache\Cache:
                break;
            default:
                throw new LimitFrequencyException('', LimitFrequencyException::CACHE_INSTANCE_ILLEGAL);
                break;
        }
        return $this->cache;
    }

    /**
     * 设置缓存
     *
     * @param \Doctrine\Common\Cache\Cache $cache doctrine缓存类
     * @return $this
     */
    public function setCache(\Doctrine\Common\Cache\Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * 根据键生成字符串key
     * @param mixed $key 键
     * @param string $prefix 前缀
     * @param string $suffix 后缀
     * @return string 键名
     */
    public function buildKey($key, $prefix='',$suffix='')
    {
        $key = md5(json_encode($key));
        return $prefix.$key.$suffix;
    }

    /**
     * 获取指定键规定时间内使用频率
     * 注意本方法会自动计数
     *
     * @param string $key 键
     * @param integer $limitTime 限制时长[单位:秒]
     * @return bool|null
     */
    /**
     * 获取指定键规定时间内使用频率
     * 注意本方法会自动计数
     *
     * @param string $key 键
     * @param integer $limitTime 限制时长[单位:秒]
     * @return integer 频率
     * @throws LimitFrequencyException 抛出异常[1键被锁定,2非法操作不是限制访问频率键]
     */
    public function getFrequency($key, $limitTime)
    {
        /* @var $cacheValue LimitFrequencyOperation */
        $cacheValue = $this->getCache()->fetch($key, $limitTime);
        switch (true) {
            case $cacheValue === false:
                $obj    = new LimitFrequencyOperation();
                $value  = $obj->getFrequency($key,$limitTime);
                $this->getCache()->save($key, $obj, $limitTime);
                break;
            default:
                $value  = $cacheValue->getFrequency($key,$limitTime);
                $this->getCache()->save($key, $cacheValue, $limitTime);
                break;
        }
        return $value;
    }
}