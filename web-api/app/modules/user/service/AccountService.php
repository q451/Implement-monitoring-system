<?php

namespace app\modules\user\service;

use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\DatetimeHelper;
use app\common\base\ApiService;
use app\modules\user\models\Account;
use app\common\base\RedisService;

class AccountService extends ApiService
{
    function __construct()
    {
        parent::init();
        $this->model = new Account();
    }

    /**
     * 登录;
     * 返回的信息包括token，用来在不同模块间传递加密信息，识别身份;
     * 
     * @param int $userId 用户名
     * @param string $password 密码
     */
    public function login($email, $password)
    {
        $info = $this->info([
            'condition' => [
                'email' => $email,
            ],
        ]);
        if(empty($info)) {
            return self::error('ERROR_INVALID_USERID', '用户不存在');
        }
        if($password != $info['password']) {
            return self::error('ERROR_INVALID_USERID', '密码不正确');
        }
        return [
            'status' => true
        ];
    }

    /**
     * 注册
     * @param int $userId 用户名
     * @param string $password 密码
     */
    public function register($email, $password, $password_again)
    {
        $info = $this->info([
            'fields' => ['id', 'email'],
            'condition' => [
                'email' => $email,
            ],
        ]);

        $createTimeStamp = DatetimeHelper::msectime();

        if(!empty($info)) {
            return self::error('ERROR_INVALID_USERID', '用户已存在');
        }
        
        if($password != $password_again){
            return self::error('ERROR_INVALID_PASSWORD', '两次密码输入不一致');
        }else {
            $this->add([
                'email' => $email,
                'password' => $password,
                
            ]);
            // self::callModuleService('user', 'UserInfoService', 'add', [
            //     'email' => $email,
            //     'create_timestamp' =>$createTimeStamp
            // ]);
        }
       
        return [
            'status' => true
        ];
    }

     /**
     * 修改密码
     */
    public function changePassword($accountInfo)
    {
        $info = $this->info([
            'condition' => [
                'email' => $accountInfo['email'],
            ],
        ]);
        if(empty($info)) {
            return self::error('ERROR_INVALID_USERID', '用户不存在');
        }
        //更新密码
        $this->update(['password' => $accountInfo['again_pw']], ['email' => $accountInfo['email']]);
    }

    /**
     * 密码加密;
     * 
     * @return string 加密后的密码
     */
    public function buildPassword($password)
    {
        return base64_encode($password);
    }
}

