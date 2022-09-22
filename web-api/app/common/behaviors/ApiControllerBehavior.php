<?php
/**
 * API控制器行为类
 * 扩展API控制器的方法和属性
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-24 21:29:25
 */

namespace app\common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use app\common\components\ApiValidator;

use Exception;

class ApiControllerBehavior extends Behavior
{
    public $userInfo = '';

    public function events()
    {
        return \yii\helpers\ArrayHelper::merge(parent::events(), [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ]);
    }

    /**
     * action请求前事件处理
     */
    public function beforeAction($event) {
        // token 校验
        Yii::$app->jwt->checkToken($this->owner->id, $event->action->id);
        $payloadData = Yii::$app->jwt->payloadData;
        if (!empty($payloadData)) {
            $this->userInfo = $payloadData;
        }

        // TODO permission校验
        // TODO rate limit
    }

    /**
     * action请求完成之后事件处理
     *
     */
    public function afterAction($event) {
        Yii::$app->response->success($event->result);
    }

    /**
     * 参数校验
     */
    public function validate() {
        $rules = $this->owner->rules;
        if (!empty($rules) && is_array($rules)) {
            // 把上传文件内容合并到 request body 里面进行验证
            $validator = new ApiValidator(\yii\helpers\ArrayHelper::merge(
                Yii::$app->request->getBodyParams(),
                $_FILES
            ), $rules);
            return $validator->validData;
        }
    }
}
