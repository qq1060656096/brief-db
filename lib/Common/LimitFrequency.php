<?php
namespace Wei\Base\Common;

use Wei\Base\Exception\LimitFrequencyException;

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
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * 设置缓存
     * @param \Doctrine\Common\Cache\Cache $cache doctrine缓存类
     */
    public function setCache(\Doctrine\Common\Cache\Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * 根据键生成字符串key
     * @param mixed $key 键
     * @param string $prefix 前缀
     * @param string $suffix 后缀
     * @return string 键名
     */
    public function buildKey($key,$prefix='',$suffix='')
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
        $value = $this->getCache()->fetch($key);
        switch (true)
        {
            case $this->valueIsLock($value)://键的值已被设置为锁定状态
                throw new LimitFrequencyException('', LimitFrequencyException::KEY_IS_LOCK);
                break;
            case $value === false://键不存在
                $value = 1;
                $result = $this->getCache()->save($key, $value, $limitTime);
                break;
            case is_numeric($value):
                $value ++;
                $result = $this->getCache()->save($key, $value);
                break;
            default://非法操作
                throw new LimitFrequencyException('', LimitFrequencyException::KEY_IS_NOT_FREQUENCY_KEY);
                break;
        }
        return $value;
    }

    /**
     * 设置键锁定时长
     *
     * @param string $key 键
     * @param integer $lockTime 锁定时长
     */
    public function setLock($key, $lockTime)
    {
        return $this->getCache()->save($key, self::LOCK_VALUE, $lockTime);
    }

    /**
     * 检查指定的值是否锁定
     *
     * @param mixed $value
     * @return bool
     */
    public function valueIsLock($value)
    {
        $return = $value === self::LOCK_VALUE ? true : false;
        return $return;
    }

}