<?php
namespace Wei\Base\Common;

/**
 * 分页类
 *
 * Class Pagination
 * @package Wei\Base\Common
 */
class Pagination
{
    /**
     * 偏移
     *
     * @var null
     */
    protected $offset = null;
    /**
     * 总条数
     *
     * @var null
     */
    protected $totalCount = null;

    /**
     * 总页数
     *
     * @var null
     */
    protected $totalPage = null;

    /**
     * 分页大小
     *
     * @var null
     */
    protected $pageSize = null;
    /**
     * 当前页
     *
     * @var null
     */
    protected $page = null;

    /**
     * 上一页
     *
     * @var null
     */
    protected $prevPage = null;

    /**
     * 下一页
     *
     * @var null
     */
    protected $nextPage = null;

    /**
     * 显示页数
     *
     * @var null
     */
    protected $showPageNum = null;

    /**
     * 显示开始分页
     *
     * @var null
     */
    protected $startShowPage = null;

    /**
     * 显示结束页
     *
     * @var null
     */
    protected $endShowPage = null;
    /**
     * 第一页
     *
     * @var null
     */
    protected $firstPage = null;

    /**
     * 最后一页
     *
     * @var null
     */
    protected $lastPage = null;

    /**
     * 构造方法
     *
     * @param integer $totalCount 总条数
     * @param integer $page 当前页
     * @param integer $pageSize 分页大小
     * @param integer $showPageNum 显示页条数
     */
    public function __construct($totalCount, $page = 1, $pageSize = 10, $showPageNum = 5)
    {
        $this->totalCount   = intval($totalCount);
        $this->page         = intval($page);
        $this->pageSize     = intval($pageSize);
        $this->showPageNum  = intval($showPageNum);
        $this->init();
    }

    /**
     * 分页初始化
     */
    protected function init()
    {
        $this->totalCount   = max(0, $this->totalCount);
        $this->page         = max(1, $this->page);
        $this->pageSize     = max(1, $this->pageSize);
        $this->firstPage    = 1;
        $this->totalPage    = ceil($this->totalCount / $this->pageSize);
        $this->lastPage     = $this->totalPage;
        $this->startShowPage = max(1, $this->page - $this->showPageNum);
        $this->endShowPage  = max($this->totalPage, $this->page + $this->showPageNum);
        $this->offset       = ($this->page -1) * $this->pageSize;

    }

    /**
     * 设置总条数
     *
     * @param integer $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = intval($totalCount);
        $this->init();
        return $this;
    }

    /**
     * 设置分页
     *
     * @param integer $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = intval($page);
        return $this;
    }


    /**
     * 设置分页大小
     *
     * @param integer $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = intval($pageSize);
        $this->init();
        return $this;
    }

    /**
     * 设置显示页条数
     *
     * @param integer $showPageNum
     * @return $this
     */
    public function setShowPageNum($showPageNum)
    {
        $this->showPageNum = intval($showPageNum);
        $this->init();
        return $this;
    }


    /**
     * 页偏移
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * 条数
     */
    public function getLimit()
    {
        $this->pageSize;
    }

    /**
     * 总条数
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * 总页数
     *
     * @return integer
     */
    public function getTotalPage()
    {
        return $this->totalPage;
    }

    /**
     * 分页大小
     *
     * @return integer
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * 当前页
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * 上一页
     *
     * @return integer
     */
    public function getPrevPage()
    {
        return $this->prevPage;
    }

    /**
     * 下一页
     *
     * @return integer
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * 显示页数
     *
     * @return integer
     */
    public function getShowPageNum()
    {
        return $this->showPageNum;
    }


    /**
     * 显示开始页
     *
     * @return integer
     */
    public function getStartShowPage()
    {
        return $this->startShowPage;
    }

    /**
     * 显示结束页
     *
     * @return integer
     */
    public function getEndShowPage()
    {
        return $this->endShowPage;
    }

    /**
     * 第一页
     *
     * @return integer
     */
    public function getFirstPage()
    {
        return $this->firstPage;
    }

    /**
     * 最后一页
     *
     * @return integer
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }


}