<?php
 
namespace app\common\components;
 
use Yii;
use yii\base\BaseObject;
use app\helpers\DatetimeHelper;
use app\common\components\Ucpaas;
use app\modules\sms\service\SmsService;
 
class SmsJob extends BaseObject implements \yii\queue\Job
{
    public $userId;
    public $sendUserId;
    public $type;
    public $appid;
    public $templateCode;
    public $param=null;
    public $mobile;
    public $uid;
    public function execute($queue)
    {
        print($this->appid);
        print($this->templateCode);
        print($this->param);
        print($this->mobile);
        print($this->uid);
        $res = (new Ucpaas)->SendSms($this->appid, $this->templateCode, $this->param, $this->mobile, $this->uid);
        print('here');
        $res = json_decode($res);
        print_r($res);
        $addData = [
            'userid' => (string)$this->userId,
            'send_user_id' => (string)$this->sendUserId,
            'phone' => (string)$this->mobile,
            'type' => (string)$this->type,
            'template_code' => (string)$this->templateCode,
            'template_param' => (string)$this->param,
            'out_id' => (string)$this->uid,
            'res_message' => (string)$res->msg ?? '',
            'response_code' => (string)$res->code ?? '',
            'res_biz_id' => (string)$res->smsid ?? '',
            'create_timestamp' => DatetimeHelper::msectime(),
        ];
        print_r($addData);
        (new SmsService)->add($addData);
    }
 
}