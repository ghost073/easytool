<?php
/**
 *
 * Author: youling073
 * Date: 2019/10/11 11:26
 * Description:  日期测试 unit
 * http://www.phpunit.cn/manual/current/zh_cn/phpunit-book.html#appendixes.assertions.static-vs-non-static-usage-of-assertion-methods
 *
 */
namespace samples\date;
include_once __DIR__.'/../../vendor/autoload.php';

use Youling073\Easytool\Date\Dateformat;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    private $_nowtime = null;
    private $dateformat = null;

    public function setUp(): void
    {
        $this->_nowtime = '2019-10-11 11:47:57';
        $this->dateformat = new Dateformat();
    }

    /**
     * 当周一
     */
    public function testMonday()
    {
        $monday = $this->dateformat->getMonday($this->_nowtime);
        $this->assertEquals('2019-10-07', $monday);
    }

    /**
     * 当周日
     */
    public function testSunday()
    {
        $sunday = $this->dateformat->getSunday($this->_nowtime);
        $this->assertEquals('2019-10-13', $sunday);
    }

    /**
     * 月份第一天
     */
    public function testMonthFirstDay()
    {
        $day = $this->dateformat->getMonthFirstDay($this->_nowtime);
        $this->assertEquals('2019-10-01', $day);
    }

    /**
     * 月份最后一天
     */
    public function testMonthLastDay()
    {
        $day = $this->dateformat->getMonthLastDay($this->_nowtime);
        $this->assertEquals('2019-10-31', $day);
    }

    /**
     * 上月第一天
     */
    public function testLastMonthFirstDay()
    {
        $day = $this->dateformat->getLastMonthFirstDay($this->_nowtime);
        $this->assertEquals('2019-09-01', $day);
    }

    /**
     * 上月份最后一天
     */
    public function testLastMonthLastDay()
    {
        $day = $this->dateformat->getLastMonthLastDay($this->_nowtime);
        $this->assertEquals('2019-09-30', $day);
    }

    /**
     * 两个日期相差几个月
     */
    public function testDiffMonthTwoDay()
    {
        $min_day = '2018-10-01';
        $max_day = '2019-10-11';

        $day = $this->dateformat->diffMonthTwoDay($min_day, $max_day);
        $this->assertEquals(12, $day);
    }

    /**
     * 前一天时间
     */
    public function testYestoday()
    {
        $day = $this->dateformat->getYestoday($this->_nowtime);
        $this->assertEquals('2019-10-10', $day);
    }

    /**
     * 上周1时间
     */
    public function testLastMonday()
    {
        $day = $this->dateformat->getLastMonday($this->_nowtime);
        $this->assertEquals('2019-09-30', $day);
    }

    /**
     * 两个日期相差几天
     */
    public function testDiffTwoDay()
    {
        $min_day = '2019-10-01';
        $max_day = '2019-10-11';

        $day = $this->dateformat->diffDayTwoDay($min_day, $max_day);
        $this->assertEquals(10, $day);
    }

    /**
     * 当前日期
     */
    public function testToday()
    {
        $day = $this->dateformat->getToday($this->_nowtime);
        $this->assertEquals('2019-10-11', $day);
    }

    /**
     * 日期几天前的时间
     */
    public function testBeforeDay()
    {
        $day = $this->dateformat->getBeforeDay($this->_nowtime,1);
        $this->assertEquals('2019-10-10', $day);
    }

    /**
     * 日期几天后的时间
     */
    public function testAfterDay()
    {
        $day = $this->dateformat->getAfterDay($this->_nowtime, 1);
        $this->assertEquals('2019-10-12', $day);
    }

    /**
     * 年份第一天
     */
    public function testYearFirstDay()
    {
        $day = $this->dateformat->getYearFirstDay($this->_nowtime);
        $this->assertEquals('2019-01-01', $day);
    }

    /**
     * 年份最后一天
     */
    public function testYearLastDay()
    {
        $day = $this->dateformat->getYearLastDay($this->_nowtime);
        $this->assertEquals('2019-12-31', $day);
    }

    /**
     * 季度第一天
     */
    public function testQuarterFirstDay()
    {
        $day = $this->dateformat->getQuarterFirstDay($this->_nowtime);
        $this->assertEquals('2019-10-01', $day);
    }

    /**
     * 季度最后一天
     */
    public function testQuarterLastDay()
    {
        $day = $this->dateformat->getQuarterLastDay($this->_nowtime);
        $this->assertEquals('2019-12-31', $day);
    }

    /**
     * 下周一日期
     */
    public function testNextMonday()
    {
        $day = $this->dateformat->getNextMonday($this->_nowtime);
        $this->assertEquals('2019-10-14', $day);
    }

    /**
     * 人性化时间，自己处理
     *
     * @test
     * @group printdata
     */
    public function showTimeFormat()
    {
         $time1 = $this->dateformat->formatTime($this->_nowtime);
         $time2 = $this->dateformat->formatTime2($this->_nowtime);
         $time3 = $this->dateformat->formatTime3($this->_nowtime);

         var_dump($time1, $time2, $time3);

        $this->assertTrue(true);
    }

    /**
     * 未来几天的日期
     *
     * @test
     * @group printdata
     */
    public function showFutureDayArr()
    {
        $day = $this->dateformat->getFutureDayArr($this->_nowtime, 2);

        var_dump($day);
        $this->assertTrue(true);
    }
}