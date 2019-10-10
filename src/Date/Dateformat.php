<?php
/**
 * 日期处理类
 *
 * @author youling073
 * @date 2019/10/10 17:23
 *
 */

namespace Youling073\Easytool\Date;

class Dateformat {
    /**
     * 根据时间获得当周一
     *
     * @param int $time
     *
     */
    public function getMonday($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date("Y-m-d",strtotime('last sunday +1 day', $time));
    }

    /**
     * 根据时间获得当周日
     *
     * @param int $time
     *
     */
    public function getSunday($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date("Y-m-d",strtotime('sunday', $time));
    }

    /**
     * 根据时间获得月份第一天
     *
     * @param    int     $time       时间戳
     *
     * @return   string              时间 ex:2013-01-01
     */
    public function getMonthFirstDay($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-01', $time);
        return $day;
    }

    /**
     * 根据时间获得月份最后一天
     *
     * @param    int     $time       时间戳
     *
     * @return   string              时间 ex:2013-01-01
     */
    public function getMonthLastDay($time) {
        $time = $this->getMonthFirstDay($time);
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-d', strtotime('+1 month -1 day', $time));
        return $day;
    }

    /**
     * 根据时间获得上月第一天
     *
     * @param    int     $time       时间戳
     *
     * @return   string              时间 ex:2013-01-01
     */
    public function getLastMonthFirstDay($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-01', strtotime('-1 month', $time));
        return $day;
    }
    /**
     * 根据时间获得上月份最后一天
     *
     * @param    int     $time       时间戳
     *
     * @return   string              时间 ex:2013-01-01
     */
    public function getLastMonthLastDay($time) {
        $time = $this->getLastMonthFirstDay($time);
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-d', strtotime('+1 month -1 day', $time));
        return $day;
    }

    /**
     * 两个日期相差几个月
     *
     * @param string $min_day 最小日期
     * @param string $max_day 最大日期
     *
     * @return int
     */
    public function diffMonthTwoDay($min_day, $max_day) {
        $min_time = is_numeric($min_day) ? $min_day : strtotime($min_day);
        $max_time = is_numeric($max_day) ? $max_day : strtotime($max_day);

        // 互换位置
        if ($min_time > $max_time) {
            $tmp     = $min_day;
            $min_day = $max_day;
            $max_day = $tmp;
        }

        $min_time = strtotime($min_day);
        $max_time = strtotime($max_day);

        $min_year = date('Y', $min_time);
        $min_month = date('n', $min_time);

        $max_year = date('Y', $max_time);
        $max_month = date('n', $max_time);

        $diff_month = ($max_year-$min_year)*12+($max_month-$min_month);
        return $diff_month;
    }

    /**
     * 根据时间获得前一天时间
     *
     * @param    mix $time   时间戳
     *
     * @return   string
     */
    public function getYestoday($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-d', strtotime('-1 day', $time));
        return $day;
    }

    /**
     * 根据时间获得上周1时间
     *
     * @param    mix $time   时间戳
     *
     * @return   string
     */
    public function getLastMonday($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-d', strtotime('-7 day last sunday +1 day', $time));
        return $day;
    }

    /**
     * 时间转换函数(把时间显示人性化)
     *
     * @param   mix     $time   时间戳或日期
     *
     * @return  string  人性化时间
     */
    public function formatTime($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        $rtime = date("m-d H:i",$time);
        $htime = date("H:i",$time);
        $time = time() - $time;
        if ($time < 60) {
            $str = '刚刚';
        }elseif($time < 60 * 60){
            $min = floor($time/60);
            $str = $min.'分钟前';
        }elseif($time < 60 * 60 * 24){
            $h = floor($time/(60*60));
            $str = $h.'小时前 '.$htime;
        }elseif($time < 60 * 60 * 24 * 3){
            $d = floor($time/(60*60*24));
            if($d==1){
                $str = '昨天 '.$rtime;
            }else{
                $str = '前天 '.$rtime;
            }
        }else{
            $str = $rtime;
        }
        return $str;
    }

    /**
     * 时间转换函数(把时间显示人性化)
     * 最后操作的时间，只具体显示最近三天的时间，一直未更新的老帖显示月日，跨年则显示年月日，当天显示具体时间，如，13:59，昨天和前天会在时间之前加上“昨天”“前天”字样
     * @param   mix     $time   时间戳或日期
     *
     * @return  string  人性化时间
     */

    public function formatTime2($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        // 当天0点时间
        $time0 = strtotime(date('Y-m-d 00:00:00'));
        // 1天前0点时间
        $time1 = strtotime('-1 day', $time0);
        // 2天前0点时间
        $time2 = strtotime('-2 day', $time0);
        $htime = date("H:i",$time);

        if ($time >= $time0) {
            $str = $htime;
        } else if ($time >= $time1) {
            $str = '昨天 '.$htime;
        } else if ($time >= $time2) {
            $str = '前天 '.$htime;
        } else if (date('Y')==date('Y', $time)) {
            // 本年
            $str = date('m-d', $time);
        } else {
            $str = date('Y-m-d', $time);
        }
        return $str;
    }

    /**
     * 时间转换函数(把时间显示人性化)
     * 当日内: 时分秒  超出当日： 月日 时分秒   超出当年： 年月日
     * @param   mix     $time   时间戳或日期
     *
     * @return  string  人性化时间
     */

    public function formatTime3($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        // 当天0点时间
        $time0 = strtotime(date('Y-m-d 00:00:00'));
        // 当年0点时间
        $time1 = strtotime(date('Y-01-01 00:00:00'));

        if ($time < $time1) {
            $str = date('Y/m/n', $time);
        } else if ($time < $time0) {
            $str = date('m/n H:i:s', $time);
        } else {
            $str = date('H:i:s', $time);
        }
        return $str;
    }

    /**
     * 获得当天起未来7天的时间
     *
     * @param  [type] $day 当天日期
     *
     * @return [type]      [description]
     */
    public function getFutureWeek($day)
    {
        $arr = array();
        $time = strtotime($day);

        for ($i=0; $i<=6; $i++)
        {
            $next_time = $time+$i*86400;
            // 日期详细信息
            $date_arr = getdate($next_time);
            $arr[] = $date_arr;
        }

        return $arr;
    }

    /**
     * 两个日期相差几天
     *
     * @param string $min_day 最小日期
     * @param string $max_day 最大日期
     *
     * @return int
     */
    public function diffDayTwoDay($min_day, $max_day) {
        $min_time = is_numeric($min_day) ? $min_day : strtotime($min_day);
        $max_time = is_numeric($max_day) ? $max_day : strtotime($max_day);

        // 互换位置
        if ($min_time > $max_time) {
            $tmp     = $min_time;
            $min_time = $max_time;
            $max_time = $tmp;
        }

        $diffTime = $max_time - $min_time; // 计算当前时间与用户注册时间差
        // 计算第两个日期相差几天
        $retainNum = floor($diffTime/86400);

        return $retainNum;
    }

    /**
     * 获得当前天数
     *
     * @param $time 时间
     * @return false|string
     */
    public function getToday($time) {
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-d', $time);
        return $day;
    }


    /**
     * 获得日期几天前的时间
     *
     * @param $time 日期
     * @param $num 几天前
     * @return false|string
     */
    public function getBeforeDay($time, $num = 0)
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-d', strtotime("-{$num} days", $time));
        return $day;
    }

    /**
     * 获得日期几天后的时间
     *
     * @param $time 日期
     * @param $num 几天前
     * @return false|string
     */
    public function getAfterDay($time, $num = 0)
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        $day = date('Y-m-d', strtotime("+{$num} days", $time));
        return $day;
    }
}