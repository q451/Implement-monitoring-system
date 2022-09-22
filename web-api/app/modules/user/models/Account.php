<?php

namespace app\modules\user\models;

use app\common\base\ApiModel;
use yii\helpers\ArrayHelper;
use app\modules\user\behaviors\AccountBehavior;

class Account extends ApiModel
{
    public static function tableName()
    {
        return '{{%sys_use}}';
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['password', 'email', 'UserName', 'REAL_NAME', 'SEX', 'PHONE', 'DESCRIPTION'], 'string'],
        ];
    }

}

