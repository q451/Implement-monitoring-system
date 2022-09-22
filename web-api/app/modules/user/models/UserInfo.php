<?php

namespace app\modules\user\models;

use app\common\base\ApiModel;
use yii\helpers\ArrayHelper;
use app\modules\user\behaviors\AccountBehavior;

class UserInfo extends ApiModel
{
    public static function tableName()
    {
        return '{{%user_info}}';
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['phone', 'age', 'create_timestamp'], 'integer'],
            [['name', 'adress', 'email'], 'string'],
        ];
    }

    /**
     * beforeSave
     *
     * @param bool $insert
     * @return void
     */
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

