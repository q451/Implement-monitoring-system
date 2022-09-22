<?php

namespace app\modules\user\models;

use app\common\base\ApiModel;
use yii\helpers\ArrayHelper;
use app\modules\user\behaviors\AccountBehavior;

class Employee extends ApiModel
{
    public static function tableName()
    {
        return '{{%employee_info}}';
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'create_timestamp'], 'integer'],
            [['username', 'gender', 'phone', 'id_card','CREATED'], 'string'],
        ];
    }
    public function fields(){
        $fields = parent::fields();

        if(isset($fields['create_timestamp'])){
            $fields['create_time'] = function($model){
                return date("Y-m-d H:i", $model->create_timestamp/1000);
            };
        }
        return $fields;
    }
}

