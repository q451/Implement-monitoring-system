<?php
 
namespace app\common\components;
 
use Yii;
use yii\base\BaseObject;
use app\modules\email\service\SendEmailService;
 
class EmailJob extends BaseObject implements \yii\queue\Job
{
    public $email_address;
    public $template_subject;
    public $template_message;
    public function execute($queue)
    {
        print("begin/n");
        print_r($this->email_address);
        print($this->template_subject);
        print($this->template_message);
//        (new SendEmailService)->send($this->email_address, $this->template_subject, $this->template_message);
        print("end");
    }
 
}