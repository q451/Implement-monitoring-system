<?php
/**
 * 讯飞声音转写实用组件类
 *
 * @author 马振领<1779813868@qq.com>
 * @date 2020-07-22 17:26:27
 */

namespace app\common\components;

use Yii;
use yii\base\Component;

class Xunfei extends Component
{
    // 账号 相关的信息
    public $appId       = '';
    public $secretKey   = '';
    
    const PREPARE_URL       = 'http://raasr.xfyun.cn/api/prepare';// 预处理 /prepare:
    const UPLOAD_URL        = 'http://raasr.xfyun.cn/api/upload';// 文件分片上传 /upload:
    const MERGE_URL         = 'http://raasr.xfyun.cn/api/merge';// 合并文件 /merge:
    const GET_PROGRESS_URL  = 'http://raasr.xfyun.cn/api/getProgress';// 查询处理进度 /getProgress:
    const GET_RESULT_URL    = 'http://raasr.xfyun.cn/api/getResult';// 获取结果 /getResult:
    const FILE_PIECE_SIZE = 1024*1024*10;
    
    public function init()
    {
        parent::init();
    }

    /**
     * 翻译接口
     *
     * @return array
     * @throws RunException
     */
    public function actionExec($file)
    {
        if (empty($file)) {
            return -1;
        }
 
        $mime = $file['type'];
 
        $appId = $this->appId;
        $secretKey = $this->secretKey;
        // var_dump($secretKey);
        $prepareData = $this->prepare($appId, $secretKey, $file);
        // var_dump($prepareData);
        if ($prepareData['ok'] != 0) {
            return -1;
        }
 
        $taskId = $prepareData['data'];
 
        $uploadData = $this->upload($appId, $secretKey, $file, $mime, $taskId);
        // var_dump($uploadData);
        if ($uploadData['ok'] != 0) {
            return -1;
        }
 
        $mergeData = $this->merge($appId, $secretKey, $taskId);
        // var_dump($mergeData);
 
        if ($mergeData['ok'] != 0) {
            return -1;
        }
 
        return $taskId;
    }
    
    public function actionGetProgress($taskId)
    {
        $getProgressData = $this->getProgress($this->appId, $this->secretKey, $taskId);
        var_dump($getProgressData);
        if ($getProgressData['ok'] != 0) {
            return false;
        }
        
        $statusData = json_decode($getProgressData['data'], true);
        if ($statusData['status'] != 9) {
            return false;
        }
        
        return true;
    }
    
    public function actionGetResult($taskId)
    {
        $getResultData = $this->getResult($this->appId, $this->secretKey, $taskId);
        var_dump($getResultData);
        if ($getResultData['ok'] != 0) {
            return false;
        }
 
        return $getResultData['data'];
    }
 
    /**
     * 预处理
     *
     * @param $appId
     * @param $secretKey
     * @param $file
     * @return mixed
     */
    public function prepare($appId, $secretKey, $file)
    {
            $ts = time();

            $sliceNum = intval($file['size'] / self::FILE_PIECE_SIZE) + 1;
            $data = [
                'app_id' => (string)$appId,
                'signa' => (string)$this->getSinga($appId, $secretKey, $ts),
                'ts' => (string)$ts,
                'file_len' => (string)$file['size'],
                'file_name' => (string)$file['name'],
                'slice_num' => $sliceNum,
                'has_participle' => (string)"true",//转写结果是否包含分词信息
            ];
            $data = http_build_query($data);
 
            $header = [
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
            ];
 
            $res = $this->curlPost(self::PREPARE_URL, $data, $header);
 
            $resultData = json_decode($res, true);
            return $resultData;
    }
 
    /**
     * 上传文件
     *
     * @param $appId
     * @param $secretKey
     * @param $file
     * @param $taskId
     * @return mixed
     */
    public function upload($appId, $secretKey, $file, $mime, $taskId)
    {
        $ts = time();
        // $curlFile = curl_file_create(
            // $file['tmp_name'],
            // $mime,
            // $file['name']
        // );
        
        $initSliceId = "aaaaaaaaaa";
        if(file_exists($file['tmp_name'])) {
			$fileContent = file_get_contents($file['tmp_name']);
            // $fp = fopen($file['tmp_name'], "rb");
            // $tmpFile = fopen($file['tmp_name'].'tmp', "wb");
            //设置缓冲
            $buffer = '';
            //一次读取的字节数
            $buffer_size = self::FILE_PIECE_SIZE;
            
            //开始循环读取$buffer_size
			$sliceNum = intval($file['size'] / self::FILE_PIECE_SIZE) + 1;
            for($i = 0; $i < $sliceNum; $i ++){
                # code...
                //读文件到缓冲区
                // $buffer = fread($fp, $buffer_size);
				// var_dump($buffer);
				// rewind($tmpFile); 
                // fwrite($tmpFile, $buffer);
                $content = substr($fileContent, $i * self::FILE_PIECE_SIZE, self::FILE_PIECE_SIZE);
				$temp_name = $file['tmp_name'].$initSliceId;
				file_put_contents($temp_name, $content);
			
                $curlFile = curl_file_create(
                    $temp_name,
                    $mime,
                    'temp'
                );
				// var_dump($curlFile);
                $data = [
                    'app_id' => (string)$appId,
                    'signa' => (string)$this->getSinga($appId, $secretKey, $ts),
                    'ts' => (string)$ts,
                    'task_id' => $taskId,
                    'slice_id' => $initSliceId,
                    'content' => $curlFile,
                ];
                
                $header = [
                    "Content-Type: multipart/form-data"
                ];
         
                $res = $this->curlPost(self::UPLOAD_URL, $data, $header);
                unlink($temp_name);
				
                $resultData = json_decode($res, true);
				// var_dump($resultData);
                $initSliceId ++;
                
                if ($resultData['ok'] != 0) {
                    // fclose($fp);
                    // fclose($tmpFile);
                    return ['ok' => -1];
                }
            }
            // if(file_exists($file['tmp_name'].'tmp')) {
                // unlink($file['tmp_name'].'tmp');
            // }
            //关闭文件
            // fclose($fp);
            // fclose($tmpFile);
            
        }
        return ['ok' => 0];
    }
 
    /**
     * 合并文件
     *
     * @param $appId
     * @param $secretKey
     * @param $taskId
     * @return mixed
     */
    public function merge($appId, $secretKey, $taskId)
    {
        $ts = time();
 
        $data = [
            'app_id' => (string)$appId,
            'signa' => (string)$this->getSinga($appId, $secretKey, $ts),
            'ts' => (string)$ts,
            'task_id' => $taskId,
        ];
        $data = http_build_query($data);
        $header = [
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
        ];
 
        $res = $this->curlPost(self::MERGE_URL, $data, $header);
 
        $resultData = json_decode($res, true);
        return $resultData;
 
    }
 
    /**
     * 查询处理进度
     *
     * @param $appId
     * @param $secretKey
     * @param $taskId
     * @return mixed
     */
    public function getProgress($appId, $secretKey, $taskId)
    {
        $ts = time();
 
        $data = [
            'app_id' => (string)$appId,
            'signa' => (string)$this->getSinga($appId, $secretKey, $ts),
            'ts' => (string)$ts,
            'task_id' => $taskId,
        ];
 
        $data = http_build_query($data);
 
        $header = [
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
        ];
 
        $res = $this->curlPost(self::GET_PROGRESS_URL, $data, $header);
 
        $resultData = json_decode($res, true);
 
        return $resultData;
    }
 
    /**
     * 获取转写结果
     *
     * @param $appId
     * @param $secretKey
     * @param $taskId
     * @return mixed
     */
    public function getResult($appId, $secretKey, $taskId)
    {
        $ts = time();
 
        $data = [
            'app_id' => (string)$appId,
            'signa' => (string)$this->getSinga($appId, $secretKey, $ts),
            'ts' => (string)$ts,
            'task_id' => $taskId,
        ];
 
        $data = http_build_query($data);
 
        $header = [
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
        ];
 
        $res = $this->curlPost(self::GET_RESULT_URL, $data, $header);
 
        $resultData = json_decode($res, true);
 
        return $resultData;
    }
 
 
    /**
     * curl
     *
     * @param $url
     * @param string $postData
     * @param string $header
     * @return bool|string
     */
    public function curlPost($url, $postData = '', $header = '')
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
     * 获取signa
     *
     * @param $appId
     * @param $secretKey
     * @param $ts
     * @return string
     */
    public function getSinga($appId, $secretKey, $ts)
    {
        $md5Str = $appId . $ts;
 
        $md5 = MD5($md5Str);
 
        $md5 = mb_convert_encoding($md5, "UTF-8");
 
        // 相当于java的HmacSHA1方法
        $signa = base64_encode(hash_hmac("sha1", $md5, $secretKey, true));
 
        return $signa;
    }
}
