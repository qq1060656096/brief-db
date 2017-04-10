<?php
namespace Wei\Base\LimitFrequency;

use Wei\Base\Exception\LimitFrequencyException;

/**
 * 限制访问频率操作
 *
 * Class LimitFrequencyData
 * @package Wei\Base\LimitFrequency
 */
class LimitFrequencyOperation
{
    /**
     * 键
     * @var mixed
     */
    protected $key         = null;
    /**
     * 值
     *
     * @var null
     */
    protected $value            = null;
    /**
     * 设置值时间
     *
     * @var null
     */
    protected $created          = null;
    /**
     * 有效时间
     *
     * @var int
     */
    protected $lifeTime         = 0;

    /**
     * 锁定有效时间
     *
     * @var null
     */
    protected $lockedLifeTime   = null;

    /**
     * 锁定时间
     *
     * @var null
     */
    protected $lockedTime       = null;
    

    /**
     * 清空数据
     */
    public function clear()
    {
        $this->key      = null;
        $this->value    = null;
        $this->created  = null;
        $this->lifeTime = 0;
        $this->lifeTime = null;
        $this->lockedLifeTime = null;

    }

    /**
     * 设置值(如果在有效时间内不会这只创建时间,过期或者没有设置就重新设置就重新设置创建时间)
     *
     * @param mixed $value 值
     * @param integer $lifeTime 限制时长[单位:秒]
     * @return $this
     */
    public function setValue($value, $lifeTime)
    {
        $nowTime        = time();
        $this->value    = $value;
        $this->lifeTime = max(0, intval($lifeTime));
        switch (true) {
            case $this->created === null://没有设置值
                $this->created = $nowTime;
                break;
            case $this->isLock()://锁定
                break;
            case $this->created === 0 || $this->lifeTime === 0://永久不有效
                break;
            case $this->created + $this->lifeTime > $nowTime://有效时间内
                break;
            default://过期
                $this->created = $nowTime;
                break;
        }
        return $this;
    }

    /**
     * 获取值
     *
     * @return false失败,否则成功
     */
    public function getValue()
    {
        $nowTime    = time();
        $value      = $this->value;
        switch (true) {
            case $this->created === null://没有设置值
                $value = false;
                break;
            case $this->isLock()://锁定
                break;
            case $this->created === 0://永久不有效
                break;
            case $this->created + $this->lifeTime > $nowTime://有效时间内
                break;
            default://过期
                $this->clear();
                $value = false;
                break;
        }
        return $value;
    }

    /**
     * 获取指定时间内使用频率
     * 注意本方法会自动计数
     *
     * @param string $key 键
     * @param integer $limitTime 限制时长[单位:秒]
     * @return integer 频率
     * @throws LimitFrequencyException 抛出异常[1键被锁定,2非法操作不是限制访问频率键]
     */
    public function getFrequency($key, $limitTime)
    {
        $this->key = $key;
        $value = $this->getValue();
        switch (true)
        {
            case $this->isLock()://键的值已被设置为锁定状态
                throw new LimitFrequencyException('', LimitFrequencyException::KEY_IS_LOCK);
                break;
            case $value === false://键不存在
                $value  = 1;
                $this->setValue($value, $limitTime);
                break;
            case is_numeric($value):
                $value++;
                $this->setValue($value, $limitTime);
                break;
            default://非法操作
                throw new LimitFrequencyException('', LimitFrequencyException::KEY_IS_NOT_FREQUENCY_KEY);
                break;
        }
        return $value;
    }

    /**
     * 设置锁定时长
     *
     * @param integer $lockTime 锁定时长[秒]
     * @return $this
     */
    public function setLock($lockTime)
    {
        $this->lockedTime = time();
        $this->lockedLifeTime = max(0, intval($lockTime));
        return $this;
    }

    /**
     * 检查是否锁定
     *
     * @return bool
     */
    public function isLock()
    {
        $nowTime        = time();
        //锁定结束时间
        $lockEndTime    = $this->lockedTime + $this->lockedLifeTime;
        $isLock         = true;
        switch (true) {
            case $this->lockedTime === null || $this->lockedLifeTime === null://未锁定
                $isLock = false;
                break;
            case $this->lockedLifeTime === 0://永久锁定
                break;
            case $lockEndTime > 0 && $lockEndTime > $nowTime://锁定中
                break;
            default://未锁定
                $isLock = false;
                break;
        }
        return $isLock;
    }

}