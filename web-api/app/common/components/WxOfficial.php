<?php
namespace app\common\components;

use app\modules\config\service\ConfigService;
use Yii;
use yii\base\Component;
use app\common\base\RedisService;
use app\helpers\DatetimeHelper;
use app\modules\survey\service\SurveyInfoService;

class WxOfficial extends Component
{
	// 
    // const WX_SECRET = '8ce9fe2a177b2170073592588826362e';
    // const WX_APPID = 'wxcbc98323a9a8bc9e';

	// 
	const WX_APPID = 'wx7c6a5aba7f8ff8d1';
	const WX_SECRET = '6ff8c509b0393eea8db386b653f8ddf0';
    const URL_SEND_ONE = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=';
    const URL_SEND_BATCH = '';

	const WEIXIN_ACCESS_TOKEN_STRING = 'WEIXIN_ACCESS_TOKEN_STRING';

    public function init()
    {
        parent::init();
    }

    private function getResult($url, $body = null, $method)
    {
        $data = $this->connection($url,$body,$method);
        if (isset($data) && !empty($data)) {
            $result = $data;
        } else {
            $result = '[]';
        }
        return $result;
    }

    /**
     * @param $url    
     * @param $body   post
     * @param $method post get
     * @return mixed|string
     */

    private function connection($url, $body,$method)
    {
		$result = '';
        if (function_exists("curl_init")) {
            $header = array(
                'Accept:application/json',
                'Content-Type:application/json;charset=utf-8',
            );
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            if($method == 'post'){
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $opts = array();
            $opts['http'] = array();
            $headers = array(
                "method" => strtoupper($method),
            );
            $headers[]= 'Accept:application/json';
            $headers['header'] = array();
            $headers['header'][]= 'Content-Type:application/json;charset=utf-8';

            if(!empty($body)) {
                $headers['header'][]= 'Content-Length:'.strlen($body);
                $headers['content']= $body;
            }

            $opts['http'] = $headers;
            $result = file_get_contents($url, false, stream_context_create($opts));
        }
        return $result;
    }

    /**
	 * 获取access_token
     */
    public function getAccessToken()
	{
		$redisService = new RedisService();
		$keyInfo['key'] = self::WEIXIN_ACCESS_TOKEN_STRING;
		$value = $redisService->getItem($keyInfo);

		if(empty($value)) {
			$expireSeconds = 7200;
			$keyInfo['expire'] = $expireSeconds;

            $conf = (new ConfigService())->getConfig('system', 'param');
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $conf['wx_appid'] . '&secret=' . $conf['wx_secret'];
			$data = $this->getResult($url, '', 'get');
			$data = json_decode($data, true);
            if(isset($data['access_token'])) {
                $value = $data['access_token'];
            }else if(isset($data['errmsg'])){
                $inputs = [];
                $inputs['name'] = '【紧急通知】微信通知白名单问题';
                $inputs['requirement'] = '<p>尊敬的管理员，您好！</p>' .
                    '<p>由于服务器ip发生变化，请将服务器新ip加入到微信公众号接口调用白名单中</p>' .
                    '<p>错误信息：' . $data['errmsg']  . '</p>';
                $surveyRange = [['user_id' => 'admin', 'name' => 'admin']];
                $inputs['is_temporary'] = 0;
                $inputs['start_timestamp'] = 0;
                $inputs['stop_timestamp'] = 0;
                $inputs['inspectType'] = 0;
                $inputs['is_special'] = 0;
                $inputs['open_survey'] = 0;
                $inputs['type'] = 1;
                $inputs['remind_times'] = [];
                $inputs['remind_contents'] = [];
                $inputs['is_remind_me'] = [];
                $inputs['is_remind_ontime'] = [];
                $inputs['survey_range_unit'] = [];
                $inputs['attachment_url'] = [];
                $inputs['cooperates'] = [];
                $inputs['inspectList'] = [];
                $inputs['xjlx'] = [];
                $inputs['function_type'] = 0;

                $ifDeleteTemporary = 0; //不删除该用户通知暂存内容
                $surveyQuestion = [];
                (new SurveyInfoService)->addOne($inputs, $surveyRange, $surveyQuestion, 'admin', $ifDeleteTemporary);
            }else {
                $inputs = [];
                $inputs['name'] = '【紧急通知】微信通知问题';
                $inputs['requirement'] = '<p>尊敬的管理员，您好！</p>' .
                    '<p>微信通知获取access_token发生未知问题请及时查看</p>';
                $surveyRange = [['user_id' => 'admin', 'name' => 'admin']];
                $inputs['is_temporary'] = 0;
                $inputs['start_timestamp'] = 0;
                $inputs['stop_timestamp'] = 0;
                $inputs['inspectType'] = 0;
                $inputs['is_special'] = 0;
                $inputs['open_survey'] = 0;
                $inputs['type'] = 1;
                $inputs['remind_times'] = [];
                $inputs['remind_contents'] = [];
                $inputs['is_remind_me'] = [];
                $inputs['is_remind_ontime'] = [];
                $inputs['survey_range_unit'] = [];
                $inputs['attachment_url'] = [];
                $inputs['cooperates'] = [];
                $inputs['inspectList'] = [];
                $inputs['xjlx'] = [];
                $inputs['function_type'] = 0;

                $ifDeleteTemporary = 0; //不删除该用户通知暂存内容
                $surveyQuestion = [];
                (new SurveyInfoService)->addOne($inputs, $surveyRange, $surveyQuestion, 'admin', $ifDeleteTemporary);
            }

			$res = $redisService->setItem($keyInfo, $value);
		}

		return $value;
    }

    /**
     * @param $openid       
     * @param $templateId  
     * @param $jumpUrl   
     * @param null $param  
     * @return mixed|string
     */
    public function sendOne($templateId, $param = null, $openid, $jumpUrl)
	{
        $url = self::URL_SEND_ONE . $this->getAccessToken();
        $body_json = array(
            'touser' => $openid,
            'template_id' => $templateId,
            'url' => $jumpUrl,
            'data' => $param,
        );
        $body = json_encode($body_json, true);
        $data = $this->getResult($url, $body,'post');

		// $res = json_decode($data);
		// if((string)$res->errcode == '40001') {
			// $expireSeconds = 7200;
			// $keyInfo['expire'] = $expireSeconds;

			// $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . self::WX_APPID . '&secret=' . self::WX_SECRET;
			// $data = $this->getResult($url, '', 'get');
			// $data = json_decode($data, true);
			// $value = $data['access_token'];

			// $res = $redisService->setItem($keyInfo, $value);
		// }
        return $data;
    }

	 /**
     * @param $openidList  
     * @param $templateId 
     * @param null $param 
     * @param $jumpUrl 
     * @return mixed|string
     */ 
	public function sendBatch($datas){
        $url = self::URL_SEND_ONE . $this->getAccessToken();
        $header = array(
            'Accept:application/json',
            'Content-Type:application/json;charset=utf-8',
        );
        $resDatas = [];
        $nowTimestamp = DatetimeHelper::msectime();
        if(count($datas) > 100) {
            $resDatas = array_merge($resDatas, $this->sendBatch(array_slice($datas, 0, 100)));
            $resDatas = array_merge($resDatas, $this->sendBatch(array_slice($datas, 100)));
        }else {
            $chList = [];
            $mh = curl_multi_init();
            foreach($datas as $key => $item) {
                $resDatas[$key] = [
                    'openid' => (string)$item['openid'],
                    'template_id' => (string)$item['templateId'],
                    'template_param' => json_encode($item['param']),
                    'create_timestamp' => $nowTimestamp
                ];
                $body_json = array(
                    'touser' => $item['openid'],
                    'template_id' => $item['templateId'],
                    'url' => $item['jumpUrl'],
                    'data' => $item['param'],
                );
                $body = json_encode($body_json, true);

                $chList[$key] = curl_init($url);
                curl_setopt($chList[$key], CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chList[$key], CURLOPT_HTTPHEADER, $header);
                curl_setopt($chList[$key],CURLOPT_POST,1);
                curl_setopt($chList[$key],CURLOPT_POSTFIELDS,$body);
                curl_setopt($chList[$key], CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($chList[$key], CURLOPT_SSL_VERIFYHOST, false);

                // 添加连接
                curl_multi_add_handle($mh, $chList[$key]);
            }
            // 同时执行查询，知道全部执行完毕
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);

            //关闭连接并获取结果
            foreach($chList as $key => $ch) {
                curl_multi_remove_handle($mh, $ch);
                $response = json_decode(curl_multi_getcontent($ch), true);

                $resDatas[$key] = array_merge($resDatas[$key], [
                    'res_message' => (string)$response['errmsg'] ?? '',
                    'response_code' => (string)$response['errcode'] ?? '',
                    'msgid' => (string)$response['msgid'] ?? ''
                ]);
            }
            curl_multi_close($mh);
        }

        return $resDatas;
    }
}