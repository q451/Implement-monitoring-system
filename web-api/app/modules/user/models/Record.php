<?php

namespace app\modules\user\models;

use app\common\base\ApiModel;
use yii\helpers\ArrayHelper;
use app\modules\user\behaviors\AccountBehavior;

class Record extends ApiModel
{
    public static function tableName()
    {
        return '{{%user_record}}';
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['type', 'is_like', 'status', 'create_timestamp'], 'integer'],
            [['userid', 'title', 'content'], 'string'],
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

