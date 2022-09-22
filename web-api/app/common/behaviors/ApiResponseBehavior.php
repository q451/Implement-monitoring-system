<?php
/**
 * ApiResponse行为扩展类
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-26 10:31:32
 */

namespace app\common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\web\Response;

class ApiResponseBehavior extends Behavior
{
    public function events()
    {
        return \yii\helpers\ArrayHelper::merge(parent::events(), [
            Response::EVENT_BEFORE_SEND => 'beforeSend',
        ]);
    }

    /**
     * response->send()前置事件
     * 修正HTTP status code，统一设置为200
     * 屏蔽错误信息暴露的一些字段
     *
     */
    public function beforeSend($event)
    {
        $response = $event->sender;

        if ($response->statusCode != 200) {
            $response->statusCode = 200;
            $data = $response->data;

            // TODO 重置错误信息，把异常信息写入log
            if (YII_ENV_PROD) {
                $data = [
                    'code' => $data['code'],
                    'name' => $data['name'],
                ];
            }
            $response->data = [
                'code' => $response->resCode['ERROR_HTTP_STATUS_NOT_200'],
                'data' => $data,
            ];
        }
        Yii::getLogger()->log(print_r(Yii::$app->request->post(),true),yii\log\Logger::LEVEL_INFO,'MyLog');//使用MyLog策略来记录info级别的日志
        Yii::getLogger()->log(print_r($response->data,true),yii\log\Logger::LEVEL_INFO,'MyLog');//使用MyLog策略来记录info级别的日志
    }
}
