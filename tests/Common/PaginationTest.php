<?php
namespace Wei\BriefDB\Tests\Common;


use Wei\BriefDB\Common\Composer;
use Wei\BriefDB\Common\Pagination;
use Wei\BriefDB\Tests\WeiTestCase;

/**
 * 分页单元测试
 *
 */
class PaginationTest extends WeiTestCase
{
    /**
     * 测试正常分页
     */
    public function test()
    {
        $pagination = new Pagination(300, 8, 20, 3);
        $this->assertEquals(300, $pagination->getTotalCount());
        $this->assertEquals(8, $pagination->getPage());
        $this->assertEquals(20, $pagination->getPageSize());
        $this->assertEquals(15, $pagination->getTotalPage());
        $this->assertEquals(3, $pagination->getShowPageNum());
        $this->assertEquals(140, $pagination->getOffset());
        $this->assertEquals(5, $pagination->getStartShowPage());
        $this->assertEquals(11, $pagination->getEndShowPage());
        $this->assertEquals(20, $pagination->getLimit());
        $this->assertEquals(7, $pagination->getPrevPage());
        $this->assertEquals(9, $pagination->getNextPage());
        $this->assertEquals(1, $pagination->getFirstPage());
        $this->assertEquals(15, $pagination->getLastPage());
    }

    /**
     * 测试分页边界值
     */
    public function test1()
    {
        $pagination = new Pagination(15, 8, 20, 3);
        $this->assertEquals(15, $pagination->getTotalCount());
        $this->assertEquals(1, $pagination->getPage());
        $this->assertEquals(20, $pagination->getPageSize());
        $this->assertEquals(1, $pagination->getTotalPage());
        $this->assertEquals(3, $pagination->getShowPageNum());
        $this->assertEquals(0, $pagination->getOffset());
        $this->assertEquals(1, $pagination->getStartShowPage());
        $this->assertEquals(1, $pagination->getEndShowPage());
        $this->assertEquals(20, $pagination->getLimit());
        $this->assertEquals(1, $pagination->getPrevPage());
        $this->assertEquals(1, $pagination->getNextPage());
        $this->assertEquals(1, $pagination->getFirstPage());
        $this->assertEquals(1, $pagination->getLastPage());



        $pagination = new Pagination(100, 25, 5, 3);
//        print_r($pagination);
        $this->assertEquals(100, $pagination->getTotalCount());
        $this->assertEquals(20, $pagination->getPage());
        $this->assertEquals(5, $pagination->getPageSize());
        $this->assertEquals(20, $pagination->getTotalPage());
        $this->assertEquals(3, $pagination->getShowPageNum());
        $this->assertEquals(95, $pagination->getOffset());
        $this->assertEquals(17, $pagination->getStartShowPage());
        $this->assertEquals(20, $pagination->getEndShowPage());
        $this->assertEquals(5, $pagination->getLimit());
        $this->assertEquals(19, $pagination->getPrevPage());
        $this->assertEquals(20, $pagination->getNextPage());
        $this->assertEquals(1, $pagination->getFirstPage());
        $this->assertEquals(20, $pagination->getLastPage());


        $pagination = new Pagination(100, -1, 5, 3);
//        print_r($pagination);
        $this->assertEquals(100, $pagination->getTotalCount());
        $this->assertEquals(1, $pagination->getPage());
        $this->assertEquals(5, $pagination->getPageSize());
        $this->assertEquals(20, $pagination->getTotalPage());
        $this->assertEquals(3, $pagination->getShowPageNum());
        $this->assertEquals(0, $pagination->getOffset());
        $this->assertEquals(1, $pagination->getStartShowPage());
        $this->assertEquals(4, $pagination->getEndShowPage());
        $this->assertEquals(5, $pagination->getLimit());
        $this->assertEquals(1, $pagination->getPrevPage());
        $this->assertEquals(2, $pagination->getNextPage());
        $this->assertEquals(1, $pagination->getFirstPage());
        $this->assertEquals(20, $pagination->getLastPage());
    }
}