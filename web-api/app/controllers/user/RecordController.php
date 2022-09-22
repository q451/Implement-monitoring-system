<?php
namespace app\controllers\user;

use Yii;
use yii\helpers\ArrayHelper;
use app\common\base\ApiController;
use app\modules\user\service\RecordService;

class RecordController extends ApiController
{
    
    public function actionAdd()
    {
        $this->rules = [
            ['userid', 'required'],
            [['userid', 'title', 'content'], 'string'],
            ['title', 'default', 'value' => ''],
            ['content', 'default', 'value' => ''],
        ];

        //相关校验，生成token
        $inputs = $this->validate();
        $info = self::callModuleService('user', 'RecordService', 'addRecord', $inputs['userid'], $inputs['title'], $inputs['content']);

        return $info;
    }

    public function actionRecordList()
    {
        
        $this->rules = [
            ['userid', 'required'],
            ['userid', 'string'],
        ];

        $inputs = $this->validate();
        $info = self::callModuleService('user', 'RecordService', 'listRecord', $inputs['userid']);

        return $info;
    }

}
