<?php
/**
 * API请求入口类
 * 以组件的方式注册到请求中去
 * 用于JWT校验、数据接口校验、请求频次限制等
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-22 15:45:53
 */

namespace app\common\components;

use Yii;
use yii\web\Request;

class ApiRequest extends Request
{
    public $allowRequestMethods = [];

    public function init()
    {
        parent::init();
        if (($language = strtolower($this->get('language'))) && in_array($language, Yii::$app->params['language'])) {
            Yii::$app->language = $language;
        }

        Yii::$app->language = strtolower(Yii::$app->language);
    }

    public function getTokenFromHeader($key)
    {
        return $this->getIsGet() ? $this->get($key) : $this->getHeaders()[$key];
    }
}
