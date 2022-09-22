<?php
/**
 * API 基础行为类
 * 扩展 controller service 等的方法和属性
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-27 10:03:55
 */

namespace app\common\behaviors;

use Yii;
use yii\base\Behavior;
use Exception;

class ApiCommonBehavior extends Behavior
{
    /**
     * 模块服务层调用方法
     *
     * @param $module string 模块名
     * @param $service string 业务服务类名
     * @param $method string 调用的方法名
     */
    public static function callModuleService($module, $service, $method)
    {
        $args = array_slice(func_get_args(), 3);

        $serviceClass = vsprintf('\app\modules\%s\service\%s', array($module, $service));
        $callable = array(new $serviceClass, $method);
        if (is_callable($callable)) {
            return call_user_func_array($callable, $args);
        } else {
            throw new Exception('unkown service call[module:' . $module . ', service:' . $service . ', method:' . $method . ']');
        }
    }

    /**
     * 异常响应方法
     *
     * @param $codeKey string 配置文件异常 code 的键值
     * @param $message mixed 提示信息
     */
    public static function error($codeKey, $message)
    {
        Yii::$app->response->error(
            Yii::$app->response->resCode[$codeKey],
            $message
        );
    }
}
