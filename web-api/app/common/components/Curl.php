<?php
/**
 * php发送curl请求
 *
 * @author 马振领<1779813868@qq.com>
 * @date 2020-10-28 19:11:02
 */

namespace app\common\components;

use Yii;
use yii\base\Component;

class Curl extends Component
{
    public function init()
    {
        parent::init();
    }

    /**
     * curl
     *
     * @param $url
     * @param string $postData
     * @param string $header
     * @return bool|string
     */
    public function post($url, $postData = '', $header = '')
    {
        //初始化
        $curl = curl_init(); //用curl发送数据给api
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
 
        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
 
        if (!empty($postData)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }
 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $response = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $response;
    }
    
    /**
     * curl
     *
     * @param $url
     * @param string $header
     * @return bool|string
     */
    public function get($url, $header = '')
    {
        $curl = curl_init();
		//设置抓取的url

        curl_setopt($curl, CURLOPT_URL, $url);        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//设置获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($curl);                     //执行命令
        curl_close($curl);                            //关闭URL请求
        return  ($data);                              //显示获得的数据
        
    }
}
