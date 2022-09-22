<?php
/**
 * API响应结果类
 * 以组件的方式注册到请求中去
 * 用于接口最终结果输出响应
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-22 15:45:53
 */

namespace app\common\components;

use Yii;
use yii\web\Response;
use app\common\behaviors\ApiResponseBehavior;

class ApiResponse extends Response
{
    public $resCode;
    public $accessOrigin;
    public $accessOriginList;
    public $allowHeaders;
    public $allowCredentials;

    public function behaviors()
    {
        return [
            'responseBehavior' => [
                'class' => ApiResponseBehavior::className(),
            ],
        ];
    }

    public function init()
    {
        parent::init();

        // 设置响应格式为JSON，否则不能使用 data 属性
        $this->format = Response::FORMAT_JSON;

        // 设置跨域请求
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? strtolower($_SERVER['HTTP_ORIGIN']) : '';
        if (in_array($origin, $this->accessOriginList)) {
            $this->headers->add('Access-Control-Allow-Origin', $origin);
        } else{
            $this->headers->add('Access-Control-Allow-Origin', $this->accessOrigin);
        }
        $this->headers->add('Access-Control-Allow-Methods', strtoupper(implode(', ', Yii::$app->request->allowRequestMethods)));
        $this->headers->add('Access-Control-Allow-Headers', $this->allowHeaders . ', ' . Yii::$app->jwt->jwtHeaderKey);
        $this->headers->add('Access-Control-Allow-Credentials', $this->allowCredentials);
    }

    /**
     * 正常响应方法
     *
     * @param $data array 响应体数组
     */
    public function success($data)
    {
        $this->data = [
            'code' => $this->resCode['NORMAL_SUCCESS'],
            'data' => $data,
        ];
        $this->send();
    }

    /**
     * 异常响应方法
     *
     * @param $code integer 响应的 code 值
     * @param $message mixed (string or array) 响应的信息提示
     */
    public function error($code, $message)
    {
        $this->data = [
            'code'    => $code,
            'message' => $message,
        ];
        $this->send();
        Yii::$app->end();
    }
}
