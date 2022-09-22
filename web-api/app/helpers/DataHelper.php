<?php
/**
 * 自定义处理数据辅助类
 *
 * @author heyijia
 * @date 2018-08-20 17:11:30
 */

namespace app\helpers;

use DateTime;
use DateTimeZone;

use yii\base\Component;

class DataHelper extends Component
{
    // 删除数组中的空数据
    public static function unsetEmptyData($datas)
    {
        $result = [];
        foreach ($datas as $data) {
            $filter = array_filter($data);
            if ($filter) {
                $result[] = $data;
            }
        }
        return $result;
    }
}
