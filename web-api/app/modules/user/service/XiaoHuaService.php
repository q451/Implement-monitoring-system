<?php

namespace app\modules\user\service;

use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\DatetimeHelper;
use app\common\base\ApiService;
use app\modules\user\models\XiaoHua;

class XiaoHuaService extends ApiService
{
    function __construct()
    {
        parent::init();
        $this->model = new XiaoHua();
    }
}

