<?php
/**
 * 自定义日期时间辅助类
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-08-25 17:11:30
 */

namespace app\helpers;

use DateTime;
use DateTimeZone;

use yii\base\Component;

class DatetimeHelper extends Component
{
    public static function toTimezone($timestamp, $fromTimezone, $toTimezone = 'Asia/Shanghai', $format = 'Y-m-d H:i:s')
    {
        $datetime = new DateTime(
            date('Y-m-d H:i:s', $timestamp),
            new DateTimeZone($fromTimezone)
        );
        $datetime->setTimezone(new DateTimeZone($toTimezone));

        if ($format) {
            return $datetime->format($format);
        } else {
            return $datetime->getTimestamp();
        }
    }

    public static function formatStudyTime($seconds)
    {
        $studyTime = '';
        $hour = floor($seconds/3600);
        $minute = floor(($seconds-3600*$hour)/60);
        $second = floor((($seconds-3600*$hour)-60*$minute)%60);
        if($hour!=0){
            $studyTime = $hour.'时'.$minute.'分'.$second.'秒';
        }
        else{
            $studyTime = $minute.'分'.$second.'秒';
       }
        return $studyTime;
    }

    // 将不同格式的时间转成时间戳
    public static function formatToTimeStamp($time)
    {
        if ($time) {
            if (is_numeric($time)) {
                $timestamp = strtotime("1900-01-01") + ($time - 2) * 24 * 60 * 60;
            } else {
                // 将年月日的格式替换成-
                $time = str_replace("年", "-", $time);
                $time = str_replace("月", "-", $time);
                $time = str_replace(".", "-", $time);
                $time = str_replace("/", "-", $time);
                $time = str_replace("日", "", $time);
                // 检查最后一个字符是否为-, 若为- 补01作为日
                if (substr($time, -1) == '-') {
                    $time .= '01';
                }
                $timestamp = strtotime($time);
            }
            return $timestamp;
        }
        return 0;
    }

    /**
     * 返回当前毫秒时间戳
     *
     */
    public static function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }
    
    /**
     * 返回上周末时间戳
     *
     */
    public static function getLastSundayMsectime()
    {
        $time = time(); //随意指定的某一个时间
        //echo $time, "\n"; //1544007032  
        $lastWeek = date("Y-m-d H:i:s", strtotime("-1 week", $time)); //先获取指定时间的上周同一时间
        //echo $lastWeek, "\n"; //2018-11-28 18:50:32 

        $currMonday = strtotime("Monday this week", $time);
        //echo $currMonday, "\n"; //1543766400

        $lastMondayTime = strtotime(date("Y-m-d", $time) == date("Y-m-d", $currMonday) ? "Monday this week" : "last Monday", strtotime($lastWeek)); //然后获取指定时间上周周一的时间
        $lastSundayTime = $lastMondayTime + 604799;
        //echo $lastMondayTime, "\n"; //1543161600 
        //echo date("Y-m-d H:i:s", $lastMondayTime), "\n";
        return 1000*$lastSundayTime;
    }
    
    
    /**
     * 返回本月初毫秒时间戳
     *
     */
    public static function getStartThisMonthMsectime()
    {
        return 1000*(strtotime(date("Y-m-01", time())));
    }
}
