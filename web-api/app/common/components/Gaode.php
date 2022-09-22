<?php
/**
 * 高德地图相关API
 *
 * @author 马振领<1779813868@qq.com>
 * @date 2021-01-07 20:34:27
 */

namespace app\common\components;

use Yii;
use yii\base\Component;

class Gaode extends Component
{
    // 账号 相关的信息
    public $key       = '';
    
    const IP_URL	       = 'https://restapi.amap.com/v3/ip'; // 根据ip获取地理位置信息:
    const WEATHER_INFO_URL = 'https://restapi.amap.com/v3/weather/weatherInfo'; // 根据城市编码获取天气信息:
    
    public function init()
    {
        parent::init();
    }

    /**
     * 根据ip获取地理位置信息
     *
     * @param $ip
     * @return array
     */
    public function getCityInfoByIp($ip)
    {
		$url = self::IP_URL.'?key='.$this->key.'&&ip='.$ip;
        
		$data = $this->get($url);
 
        return json_decode($data, true);
    }
	
    /**
     * 根据城市编码获取天气信息
     *
     * @param $cityCode
     * @return array
     */
    public function getWeatherInfoByCityCode($cityCode)
    {
		$url = self::WEATHER_INFO_URL.'?key='.$this->key.'&&city='.$cityCode;
        
		$data = $this->get($url);
 
        return json_decode($data, true);
    }
    
 
    /**
     * curl get
     *
     * @param $url
     * @return bool|string
     */
    public function get($url)
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
