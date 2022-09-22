<?php
/**
 * 字符串匹配实用组件类
 *
 * @author mazhenling<1779813868@qq.com>
 * @date 2021-10-15 19:34
 */

namespace app\common\utils;

use Yii;
use yii\base\Component;

class StringMatch extends Component
{
    protected static $dict  = [];
    protected static $suffix = '';

    /**
     * @param $dict array 字典，['北京市'=>1,'天津市'=>1]
     * @param $query  string  query词
     * @param $suffix string  后缀
     * @return mixed
     */
    public static function match(&$dict,$query,$suffix = ''){
        self::$dict = $dict;
        self::$suffix = $suffix;
        $suffix?self::matchLabelWithSuffix($query,$matched):self::matchLabel($query,$matched);
        return $matched;
    }

    private static function matchLabel($str, &$matched, $matchAll=false)
    {
        $len = mb_strlen($str,'utf-8');

        if($len < 2) {
            return;
        }

        while($len>1) {
            $substr = mb_substr($str, 0, $len, 'utf-8');
            if(isset(self::$dict[$substr])) {
                $matched = $substr;
                return;
            } else {
                $len--;
            }
        }

        $str = mb_substr($str, 1, null, 'utf-8');

        self::matchLabel($str, $matched, $matchAll);
    }

    private static function matchLabelWithSuffix($str, &$matched, $matchAll=false)
    {
        $len = mb_strlen($str,'utf-8');

        if($len < 2) {
            return;
        }

        while($len>1) {
            $substr = mb_substr($str, 0, $len, 'utf-8');
            if(isset(self::$dict[$substr])) {
                $matched = $substr;
                return;
            }elseif(isset(self::$dict[$substr.self::$suffix])){
                $matched = $substr.self::$suffix;
                return;
            } else {
                $len--;
            }
        }

        $str = mb_substr($str, 1, null, 'utf-8');

        self::matchLabel($str, $matched, $matchAll);
    }

    // 获取最长公共子串
    public static function getLongestCommonSubstr($str1, $str2) {
        $temp = array();

        $length1 = mb_strlen($str1);
        $length2 = mb_strlen($str2);
        for ($i = 0; $i < $length1; $i ++) {
            for ($j = 0; $j < $length2; $j ++) {
                $n = ($i - 1 >= 0 && $j - 1 >= 0) ? $temp[$i - 1][$j - 1] : 0; // 赋值为截止到上一字符的子串长度
                $n = (mb_substr($str1, $i, 1) == mb_substr($str2, $j, 1)) ? $n + 1 : 0; // 如果该字符也相等，则子串长度+1
                $temp[$i][$j] = $n;
            }
        }

        foreach ($temp as $val) {
            $max = max($val);
            foreach ($val as $key1 => $val1) {
                if ($val1 == $max && $max > 0) {
                    $cdStr[$max] = mb_substr($str2, $key1 - $max + 1, $max); // 存储截止到每一个字符的最长公共子串
                }
            }
        }

        if(!empty($cdStr)) {
            ksort($cdStr); // 找到最长公共子串并返回
            $longestCommonSubstr = end($cdStr);

            return $longestCommonSubstr;
        }

        return false;
    }

    public static function postCheck($post) {
        if (!get_magic_quotes_gpc()) {  // 判断magic_quotes_gpc是否为打开
            $post = addslashes($post);  // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
        }
//        $post = str_replace("_", "\_", $post);  // 把 '_'过滤掉
//        $post = str_replace("%", "\%", $post);  // 把 '%'过滤掉
//        $post = nl2br($post);  // 回车转换
//        $post = htmlspecialchars($post);  // html标记转换
        return $post;
    }

    //正则匹配获取img标签src内容-多个
    public static function getImgAllSrc($tag) {
        if(empty($tag)) {
            return [];
        }
        $matches = [];
        preg_match_all('/(src)=("[^"]*")/i', $tag, $matches);

        $ret = array();
        foreach($matches[0] as $i => $v) {
            $ret[] = trim($matches[2][$i],'"');
        }

        return $ret;
    }

    public static function extractStringFromHtml($htmlString) {
        if(empty($htmlString)) {
            return '';
        }
        $htmlString = str_replace("<br>","\r\n", $htmlString);
        $htmlString = str_replace("</p>","\r\n", $htmlString);
        // 把一些预定义的 HTML 实体转换为字符
        // 预定义字符是指:<,>,&等有特殊含义(<,>,用于链接签,&用于转义),不能直接使用
        $htmlString = htmlspecialchars_decode($htmlString);

        // 将空格去除
        // $html_string = str_replace(" ", "", $html_string);

        // 去除字符串中的 HTML 标签
        $contents = strip_tags($htmlString);

        return $contents;
    }
}
