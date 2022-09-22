<?php
/**
 * Teacher行为类和时间触发
 */

namespace app\modules\user\behaviors;

use app\common\Consts;
use app\modules\common\service\TeacherPositionService;
use app\modules\common\service\UnitSecondService;
use app\modules\common\service\UnitThirdService;
use app\modules\common\service\XinxiColumnTeacherInfoChildService;
use app\modules\common\service\XinxiColumnTeacherInfoService;
use app\modules\user\models\XinxiTeacherInfoByClassification;
use app\modules\user\service\XinxiTeacherInfoByClassificationService;
use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use app\modules\user\models\Teacher;
use app\modules\user\service\AccountService;
use app\modules\files\service\UserFileSpaceService;

use Exception;

class TeacherBehavior extends Behavior
{
    public function events()
    {
        return \yii\helpers\ArrayHelper::merge(parent::events(), [
            Teacher::EVENT_AFTER_INSERT => 'afterInsert',
            Teacher::EVENT_AFTER_UPDATE => 'afterUpdate',
            // Teacher::EVENT_BEFORE_INSERT => 'beforeInsert',
            // Teacher::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ]);
    }

    public function afterInsert($event)
    {
        // 新增老师同时新增对应账户
        (new AccountService)->defaultInstructorAccount($event->sender->userid);
    }
}
