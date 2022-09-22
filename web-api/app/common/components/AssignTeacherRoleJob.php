<?php
 
namespace app\common\components;
 
use Yii;
use yii\base\BaseObject;
use app\helpers\DatetimeHelper;
use yii\helpers\ArrayHelper;
use app\modules\common\service\CourseService;
use app\modules\user\service\UserRoleService;
use app\modules\user\service\GroupService;
use app\modules\user\service\GroupRangeService;
use app\modules\common\service\RoleService;
 
class AssignTeacherRoleJob extends BaseObject implements \yii\queue\Job
{
    public $teacherIdList;
    public function execute($queue)
    {
        print("begin/n");
        print_r($this->teacherIdList);
        
        $roleCommonTeacherData = [];
        $roleCommonTeacherId = 6;
        foreach($this->teacherIdList as $k => $leaderId)
        {
            $roleCommonTeacherData[$k]['is_student'] = 0;
            $roleCommonTeacherData[$k]['user_id'] = $leaderId;
            $roleCommonTeacherData[$k]['assignRoleList'] = [$roleCommonTeacherId];
        }
        (new UserRoleService)->assign($roleCommonTeacherData);
        
        
        $notDeleteRoleIdList = (new RoleService)::notDeleteRoleIdList;
        print_r($notDeleteRoleIdList);
        $groupIdList = ArrayHelper::getColumn((new GroupService)->lists([
            'condition' => [
                'and',
                ['in', 'role_id', $notDeleteRoleIdList],
                ['status' => 1],
            ],
        ]), 'id');
        print_r($groupIdList);
        $data = [];
        $i = 0;
        foreach($this->teacherIdList as $teacherId)
        {
            foreach($groupIdList as $groupId)
            {
                $data[$i]['group_id'] = $groupId;
                $data[$i]['user_id'] = $teacherId;
                $data[$i]['status'] = 1;
                $i ++;
            }
        }
        print_r($data);
        print(date("Y-m-d H:i:s", time()));
        (new GroupRangeService)->saveMany($data, ['group_id', 'user_id']);
        
        $teacherList = (new CourseService)->lists([
            'fields' => ['teacherId'],
            'condition' => ['and',['in', 'teacherId', $this->teacherIdList]],
            'orderby' => ['teacherId' => SORT_ASC],
            'groupby' => ['teacherId'],
        ]);
        print_r($teacherList);
        $assignList = [];
        foreach($teacherList as $i => $teacherInfo)
        {
            $assignList[$i]['is_student'] = 0;
            $assignList[$i]['user_id'] = $teacherInfo['teacherId'];
            $assignList[$i]['assignRoleList'] = [3];
        }
        print_r($assignList);
        (new UserRoleService)->assign($assignList);
        
        print("end");
    }
 
}