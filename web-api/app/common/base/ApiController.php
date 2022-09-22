<?php
/**
 * API控制器入口类
 * 所有API接口类要继承此类
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-22 15:31:23
 */

namespace app\common\base;

use Yii;
use yii\web\Controller;
use app\common\behaviors\ApiCommonBehavior;
use app\common\behaviors\ApiControllerBehavior;

class ApiController extends Controller
{
    public $rules = [];
    public $timeNow;

    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'actions' => [
                    '*' => Yii::$app->request->allowRequestMethods,
                ],
            ],
            'commonBehavior' => [
                'class' => ApiCommonBehavior::className(),
            ],
            'controllerBehavior' => [
                'class' => ApiControllerBehavior::className(),
            ],
        ]);
    }

    public function init()
    {
        parent::init();
        $this->timeNow = Yii::$app->params['timeNow'];
        $this->enableCsrfValidation = false;
    }
}
