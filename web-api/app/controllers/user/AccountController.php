<?php
/**
 * 用户帐号控制器
 *
 */

namespace app\controllers\user;

use Yii;
use yii\helpers\ArrayHelper;
use app\common\base\ApiController;
use app\modules\user\service\AccountService;
use app\common\base\RedisService;
use app\helpers\DatetimeHelper;

class AccountController extends ApiController
{
    /**
     * 登陆;
     */
    public function actionLogin()
    {
        $this->rules = [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'string'],
        ];

        //相关校验，生成token
        $inputs = $this->validate();
        $info = self::callModuleService('user', 'AccountService', 'login', $inputs['email'], $inputs['password']);

        //记录本次登陆相关信息
		$ip = Yii::$app->request->userIP;
        return [
            'info' => $info,
            'ip' => $ip
        ];
    }

    /**
     * 注册
     */
    public function actionRegister()
    {
        $this->rules = [
            [['email', 'password', 'password_again'], 'required'],
            [['email', 'password', 'password_again', 'type'], 'string'],
        ];

        $inputs = $this->validate();
        
        $info = self::callModuleService('user', 'AccountService', 'register', $inputs['email'],
            $inputs['password'], $inputs['password_again']);

        return $info;
    }

    /**
     * 改密码;
     */
    public function actionChangepw()
    {
        $this->rules = [
            [['email','again_pw', 'asset_pw'], 'required'],
            [['email', 'again_pw', 'asset_pw'], 'string'],
        ];
        $inputs = $this->validate();
        
        if($inputs['again_pw'] != $inputs['asset_pw']) {
            return self::error('ERROR_INVALID_PASSWORD_AGAIN', '两次输入的新密码不正确');
        }

        self::callModuleService('user', 'AccountService', 'changePassword', $inputs);
        return [
            'status' => true
        ];
    }
    /**
     * 改个人信息;
     */
    public function actionUserInfo()
    {
        $this->rules = [
            ['email', 'required'],
            ['email', 'string'],
        ];
        $inputs = $this->validate();

        $info = self::callModuleService('user', 'AccountService', 'info', [
            
            'condition' => [
                'email' => $inputs['email']
            ]
        ]);
        return $info;
        
    }
    /**
     * 改个人信息;
     */
    public function actionChangeInfo()
    {
        $this->rules = [
            ['email', 'required'],
            [['email', 'UserName', 'REAL_NAME', 'SEX', 'PHONE', 'DESCRIPTION'], 'string'],
            ['UserName', 'default', 'value' => ''],
            ['REAL_NAME', 'default', 'value' => ''],
            ['SEX', 'default', 'value' => ''],
            ['PHONE', 'default', 'value' => ''],
            ['DESCRIPTION', 'default', 'value' => ''],
        ];
        $inputs = $this->validate();
        $info = self::callModuleService('user', 'AccountService', 'info', [
            'condition' => [
                'email' => $inputs['email']
            ]
        ]);
        if(empty($info)) {
            return self::error('ERROR_INVALID_PASSWORD_AGAIN', '用户不存在');
        }
        self::callModuleService('user', 'AccountService', 'update', 
            [
                'UserName' => $inputs['UserName'],
                'REAL_NAME' => $inputs['REAL_NAME'],
                'SEX' => $inputs['SEX'],
                'PHONE' => $inputs['PHONE'],
                'DESCRIPTION' => $inputs['DESCRIPTION'],
            ],
            [
                'email'=>$inputs['email']
            ]
        );
        
        return [
            'status' => true
        ];
    }

    public function actionTest()
    {
        $data = self::callModuleService('user', 'OldpersonService', 'add', [
            'username' => "inputs['name']",
            'gender' => "inputs['gender']",
            'phone' => "inputs['phone']",
            'id_card' => "inputs['card']",
        ]);
        return $data;
    }

    /**
     * 通知
     */
    public function actionNotify()
    {
        $data = self::callModuleService('user', 'NotifyService', 'lists', [
            'condition' => [
                'status' => 1,
                'is_read' => 0
            ]
        ]);
        if (!empty($data)) {
            foreach ($data as &$value) {
                if ($value['event_type'] == 0) {
                    $value['type'] = '情感检测';
                }
                if ($value['event_type'] == 1) {
                    $value['type'] = '义工交互检测';
                }
                if ($value['event_type'] == 2) {
                    $value['type'] = '陌生人检测';
                }
                if ($value['event_type'] == 3) {
                    $value['type'] = '摔倒检测';
                }
                if ($value['event_type'] == 4) {
                    $value['type'] = '禁止区域入侵检测';
                }
            }
        }
        
        return $data;
    }
    //删除一条警报
    public function actionDeletenotify()
    {
        $this->rules = [
            ['id', 'required'],
            ['id', 'integer'],
        ];
        $inputs = $this->validate();

        self::callModuleService('user', 'NotifyService', 'update', ['status' => 0],['id'=>$inputs['id']]);
        
        return[
            'status' => true
        ];
    }

    //阅读一条警报
    public function actionReadNotify()
    {
        $this->rules = [
            ['id', 'required'],
            ['id', 'integer'],
        ];
        $inputs = $this->validate();

        self::callModuleService('user', 'NotifyService', 'update', ['is_read' => 1],['id'=>$inputs['id']]);
        
        return[
            'status' => true
        ];
    }

    //恢复一条警报
    public function actionRecoryNotify()
    {
        $this->rules = [
            ['id', 'required'],
            ['id', 'integer'],
        ];
        $inputs = $this->validate();

        self::callModuleService('user', 'NotifyService', 'update', ['status' => 1],['id'=>$inputs['id']]);
        
        return[
            'status' => true
        ];
    }

    //获取未读，已读，已删除通知
     public function actionDetailNotify()
     {
        $read = self::callModuleService('user', 'NotifyService', 'lists', [
            'condition' => [
                'status' => 1,
                'is_read' => 1
            ]
        ]);
        $unread = self::callModuleService('user', 'NotifyService', 'lists', [
            'condition' => [
                'status' => 1,
                'is_read' => 0
            ]
        ]);
        $del = self::callModuleService('user', 'NotifyService', 'lists', [
            'condition' => [
                'status' => 0
            ]
        ]);
        if (!empty($read)) {
            foreach ($read as &$value) {
                if ($value['event_type'] == 0) {
                    $value['type'] = '情感检测';
                }
                if ($value['event_type'] == 1) {
                    $value['type'] = '义工交互检测';
                }
                if ($value['event_type'] == 2) {
                    $value['type'] = '陌生人检测';
                }
                if ($value['event_type'] == 3) {
                    $value['type'] = '摔倒检测';
                }
                if ($value['event_type'] == 4) {
                    $value['type'] = '禁止区域入侵检测';
                }
            }
        }
        if (!empty($del)) {
            foreach ($read as &$value) {
                if ($value['event_type'] == 0) {
                    $value['type'] = '情感检测';
                }
                if ($value['event_type'] == 1) {
                    $value['type'] = '义工交互检测';
                }
                if ($value['event_type'] == 2) {
                    $value['type'] = '陌生人检测';
                }
                if ($value['event_type'] == 3) {
                    $value['type'] = '摔倒检测';
                }
                if ($value['event_type'] == 4) {
                    $value['type'] = '禁止区域入侵检测';
                }
            }
        }
        if (!empty($unread)) {
            foreach ($read as &$value) {
                if ($value['event_type'] == 0) {
                    $value['type'] = '情感检测';
                }
                if ($value['event_type'] == 1) {
                    $value['type'] = '义工交互检测';
                }
                if ($value['event_type'] == 2) {
                    $value['type'] = '陌生人检测';
                }
                if ($value['event_type'] == 3) {
                    $value['type'] = '摔倒检测';
                }
                if ($value['event_type'] == 4) {
                    $value['type'] = '禁止区域入侵检测';
                }
            }
        }
        
        return [
            'read' => $read,
            'unread' => $unread,
            'recycle' => $del,
        ];
     }
    //laoren录入
    public function actionOldPeopleManage()
    {
        $this->rules = [
            [['name', 'gender', 'phone', 'card', 'firstguardian_name','email'], 'required'],
            [['name', 'gender', 'phone', 'card', 'firstguardian_name','email'], 'string'],
        ];
        $inputs = $this->validate();
        $info = self::callModuleService('user', 'OldpersonService', 'info', [
            'condition' => [
                'status' => 1,
                'id_card' => $inputs['card']
            ]
        ]);
        if(!empty($info)){
            return self::error('ERROR_INVALID_PASSWORD', '此人已存在');
        }
        $createTimeStamp = DatetimeHelper::msectime();
        self::callModuleService('user', 'OldpersonService', 'add', [
            'username' => $inputs['name'],
            'gender' => $inputs['gender'],
            'phone' => $inputs['phone'],
            'id_card' => $inputs['card'],
            'firstguardian_name' => $inputs['firstguardian_name'],
            'firstguardian_email' => $inputs['email'],
            'create_timestamp' => $createTimeStamp,
        ]);
        
        return[
            'status' => true
        ];
    }

     //元录入
    public function actionPeopleManage()
     {
        $this->rules = [
            [['name', 'gender', 'phone', 'card', 'type'], 'required'],
            [['name', 'gender', 'phone', 'card', 'type'], 'string'],
        ];
        $inputs = $this->validate();
        $createTimeStamp = DatetimeHelper::msectime();
        if ($inputs['type'] == 1) {

            $info = self::callModuleService('user', 'VolunterService', 'info', [
                'condition' => [
                    'status' => 1,
                    'id_card' => $inputs['card']
                ]
            ]);
            if(!empty($info)){
                return self::error('ERROR_INVALID_PASSWORD', '此人已存在');
            }
            self::callModuleService('user', 'VolunterService', 'add', [
                'username' => $inputs['name'],
                'gender' => $inputs['gender'],
                'phone' => $inputs['phone'],
                'id_card' => $inputs['card'],
                'create_timestamp' => $createTimeStamp,
            ]);
        }
        if ($inputs['type'] == 2) {

            $info = self::callModuleService('user', 'EmployeeService', 'info', [
                'condition' => [
                    'status' => 1,
                    'id_card' => $inputs['card']
                ]
            ]);
            if(!empty($info)){
                return self::error('ERROR_INVALID_PASSWORD', '此人已存在');
            }
            self::callModuleService('user', 'EmployeeService', 'add', [
                'username' => $inputs['name'],
                'gender' => $inputs['gender'],
                'phone' => $inputs['phone'],
                'id_card' => $inputs['card'],
                'create_timestamp' => $createTimeStamp,
            ]);
        }

        return[
             'status' => true
         ];
     }
    //老人录入
    public function actionOldPeopleInfo()
    {
        $info = self::callModuleService('user', 'OldpersonService', 'lists', [
            'fields' => ['id','username', 'gender', 'phone', 'id_card', 'firstguardian_name','firstguardian_email','status','create_timestamp'],
            'condition' => [
                'status' => 1,
            ]
        ]);
       return $info;
    }
    //志愿者人录入
    public function actionVolunteerInfo()
    {
        $info = self::callModuleService('user', 'VolunterService', 'lists', [
            'fields' => ['id','username', 'gender', 'phone', 'id_card','status','create_timestamp'],
            'condition' => [
                'status' => 1,
            ]
        ]);
       return $info;
    }

    //志愿者人录入
    public function actionEmployInfo()
    {
        $info = self::callModuleService('user', 'EmployeeService', 'lists', [
            'fields' => ['id','username', 'gender', 'phone', 'id_card','status','create_timestamp'],
            'condition' => [
                'status' => 1,
            ]
        ]);
       return $info;
    }

    //删除老人
    public function actionDelOldPeople()
    {
        $this->rules = [
            ['id', 'required'],
            ['id', 'integer'],
        ];
        $inputs = $this->validate();

        self::callModuleService('user', 'OldpersonService', 'update', ['status' => 0],['id'=>$inputs['id']]);
        
        return[
            'status' => true
        ];
    }
    //删除志愿者
    public function actionDelVolunteer()
    {
        $this->rules = [
            ['id', 'required'],
            ['id', 'integer'],
        ];
        $inputs = $this->validate();

        self::callModuleService('user', 'VolunterService', 'update', ['status' => 0],['id'=>$inputs['id']]);
        
        return[
            'status' => true
        ];
    }
    //删除工作人员
    public function actionDelEmploy()
    {
        $this->rules = [
            ['id', 'required'],
            ['id', 'integer'],
        ];
        $inputs = $this->validate();

        self::callModuleService('user', 'EmployeeService', 'update', ['status' => 0],['id'=>$inputs['id']]);
        
        return[
            'status' => true
        ];
    }
}
