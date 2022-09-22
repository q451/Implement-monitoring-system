<?php

namespace app\modules\user\models;

use app\common\base\ApiModel;
use yii\helpers\ArrayHelper;
use app\modules\user\behaviors\AccountBehavior;

class Notify extends ApiModel
{
    public static function tableName()
    {
        return '{{%event_info}}';
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['event_type','oldperson_id','status','is_read'], 'integer'],
            [['event_date', 'event_location', 'event_desc'], 'string'],
        ];
    }

    /**
     * beforeSave
     *
     * @param bool $insert
     * @return void
     */
    
}

