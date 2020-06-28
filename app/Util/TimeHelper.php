<?php


namespace App\Util;


class TimeHelper
{

    const HOUR_S = 60 * 60;  //每小时秒数

    const DAY_S = 60 * 60 * 24; //每天秒数

    const WEEK_S = 60 * 60 * 24 * 7; //每周秒数


    public static function NowTime()
    {
        return date('Y-m-d H:i:s');
    }

    public static function NowDate()
    {
        return date('Y-m-d');
    }

    public static function YesterdayDate()
    {
        return date("Y-m-d", strtotime("-1 day"));
    }

    public static function TomorrowDate()
    {
        return date("Y-m-d", strtotime("+1 day"));
    }

    //标准时间格式转换为日期
    public static function strtoDate($time)
    {
        return date("Y-m-d", strtotime($time));
    }

    //时间戳转换为时间文案
    public static function TimeToText($time)
    {
        switch (date("Y-m-d", $time)) {
            case self::YesterdayDate() :
                $text = '昨日';
                break;
            case self::TomorrowDate() :
                $text = '明日';
                break;
            case self::NowDate() :
                $text = '今日';
                break;
            default :
                $text = date("m月d日", $time);
                break;
        }
        $H_text = date("H", $time) . '点';
        $i = date("i", $time);
        $i_text = ($i == '00') ? '' : ($i == '30' ? '半' : $i . '分');
        return $text . $H_text . $i_text;
    }


    /**
     * 获取下一个周数的日期或时间戳 , 注意 周日的周数为0
     * @param $weeknum int 周几
     * @param bool $datetime 返回为时间戳还是日期 默认为日期
     * @return bool|false|float|int|string
     */
    public static function NextWeekNum($weeknum, $datetime = true)
    {
        if (!in_array($weeknum, [0, 1, 2, 3, 4, 5, 6])) {
            return false;
        }
        $now = strtotime(date('Y-m-d'));
        $nowDay = date('w', $now);
        if ($nowDay == $weeknum) {
            $returnTime = $now + self::WEEK_S;
        } else if ($nowDay < $weeknum) {
            $day_num = $weeknum - $nowDay;
            $returnTime = $now + ($day_num * self::DAY_S);
        } else {
            $day_num = $nowDay - $weeknum;
            $returnTime = $now + (7 - $day_num) * self::DAY_S;
        }
        return $datetime ? date('Y-m-d H:i:s', $returnTime) : $returnTime;
    }

    /**
     * 把秒数转换为时分秒的格式
     * @param Int $times 时间，单位 秒
     * @return String
     */
    public static function SecToTime($times)
    {
        if ($times < 3600 * 24) {
            return gmstrftime('%H:%M:%S', $times);
        }
        $result = '00:00:00';
        if ($times > 0) {
            $hour = floor($times / 3600);
            $minute = floor(($times - 3600 * $hour) / 60);
            $second = floor((($times - 3600 * $hour) - 60 * $minute) % 60);
            $result = $hour . ':' . $minute . ':' . $second;
        }
        return $result;
    }
}