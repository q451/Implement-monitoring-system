<?php

namespace app\modules\user\models;

use app\common\base\ApiModel;
use yii\helpers\ArrayHelper;
use app\modules\user\behaviors\AccountBehavior;

class CarStudy extends ApiModel
{
    public static function tableName()
    {
        return '{{%car_exercise}}';
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'type', 'type_car'], 'integer'],
            [['question', 'answer', 'explains', 'url', 'item1', 'item2', 'item3', 'item4'], 'string'],
        ];
    }

}

