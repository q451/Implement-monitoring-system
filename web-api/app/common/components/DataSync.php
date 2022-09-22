<?php
/**
 * 向信息中心获取数据组件
 *
 * @author heyjija
 * @date 2019-06-2 10:47:43
 */

namespace app\common\components;

use Yii;
use Requests;
use yii\base\Component;

class DataSync extends Component
{
    public $clientId;
    public $clientSecret;
    public $username;
    public $password;
    public $getTokenUrl;
    public $getStudentListUrl;
    public $getStudentDetailUrl;

    public function init()
    {
        parent::init();
    }

    /**
     * 获取申请的token
     *
     */
    public function getToken()
    {
        $params = [
            'grant_type' => 'password',
            'client_id'  => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $this->username,
            'password' => $this->password,
        ];
        $response = Requests::post($this->getTokenUrl, [], $params, []);
        $body = json_decode($response->body, true);
        print_r("params:");
        print_r($params);
        print_r("\nrequest url:");
        print_r($this->getTokenUrl);
        print_r("\nresponse body:");
        print_r($body);
        return $body;
    }

    // 获取全部学生列表
    public function getStudentList($token, $format = 'json', $offset = 0, $limit = 1)
    {
        $params = [
            'format' => $format,
            'offset' => $offset,
            'limit' => $limit,
            'dqztm' => '01',
        ];
        $getStudentListUrl = $this->getStudentListUrl . '?' . http_build_query($params);
        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];
        $response = Requests::get($getStudentListUrl, $headers, []);
        $body = json_decode($response->body, true);
        print_r("\nget student list header:");
        print_r($headers);
        print_r("\nget student list params:");
        print_r($params);
        print_r("\nrequest url:");
        print_r($getStudentListUrl);
        print_r("\nresponse body:");
        print_r($body);
        return $body;
    }

    // 获取学生个体信息
    public function getStudentDetail($studentId, $token, $format = 'json', $limit = 1)
    {
        $getStudentDetailUrl = sprintf($this->getStudentDetailUrl, $studentId);
        $params = [
            'format' => $format,
            //'limit' => $limit,
        ];
        if ($params) {
            $getStudentDetailUrl .= '?' . http_build_query($params);
        }
        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];
        $response = Requests::get($getStudentDetailUrl, $headers, []);
        print_r($headers);
        $body = json_decode($response->body, true);
        print_r($response);exit();
        return $body;
    }
}
