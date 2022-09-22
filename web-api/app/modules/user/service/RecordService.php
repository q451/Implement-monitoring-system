<?php

namespace app\modules\user\service;

use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\DatetimeHelper;
use app\common\base\ApiService;
use app\modules\user\models\Record;

class RecordService extends ApiService
{
    function __construct()
    {
        parent::init();
        $this->model = new Record();
    }

    public function addRecord($userId, $title, $content)
    {
        $createTimeStamp = DatetimeHelper::msectime();

        $this->add([
            'userid' => $userId,
            'title' => $title,
            'content' => $content,
            'create_timestamp' =>$createTimeStamp
        ]);

        return [
            'status' => true
        ];
    }

    public function listRecord($userId)
    {
        $info = $this->lists([
            'condition' => [
                'userid' => $userId,
                'status' => 1,
            ],
        ]);
        
        return $info;
    }

}

