<?php
/**
 * 七牛云存储组件
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-06-23 10:47:43
 */

namespace app\common\components;

use Yii;
use yii\base\Component;
use Qiniu\Auth;
use Qiniu\Zone;
use Qiniu\Config;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

class Qiniu extends Component
{
    public $accessKey;
    public $secretKey;
    public $auth;
    public $defaultBucket;
    public $privateBucket;
    public $cdnHost;

    public function init()
    {
        parent::init();
        $this->auth = new Auth($this->accessKey, $this->secretKey);
    }

    /**
     * 上传文件到七牛
     *
     * @param string $filePath 需要上传的文件路径
     * @param string $key 在七牛上存储的文件名
     *
     * @return string 上传后文件访问的地址
     */
    public function upload($filePath, $key, $bucket = null)
    {
        $zone = Zone::zonez1(); //华北

        $config = new Config($zone);
        $uploadMgr = new UploadManager();
        $bucket = empty($bucket) ? $this->defaultBucket : $bucket;
        $token = $this->auth->uploadToken($bucket);
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_QINIU_UPLOAD_FAILED'],
                'Upload to qiniu failed.'
            );
        } else {
            return $ret['key'];
        }
    }

    /**
     * 设置文件生存时间，$days=0代表取消生存时间;
     * @param integer $days 生存时间，单位：天
     * @param string $key 在七牛上存储的文件名
     *
     * @return null
     */
    public function setSurviveDays($days, $key, $bucket = null)
    {
        $zone = Zone::zonez1(); //华北

        $config = new Config($zone);
        $bucket = empty($bucket) ? $this->defaultBucket : $bucket;
        $bucketManager = new BucketManager($this->auth, $config);
        $error = $bucketManager->deleteAfterDays($bucket, $key, $days);
        if($error !== null){
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_QINIU_DELETE_AFTER_DAYS_FAILED'],
                '设置文件生命周期失败'
            );
        }
    }

    /**
     * 删除文件;
     * @param string $key 在七牛上存储的文件名
     *
     * @return null
     */
    public function removeFile($key, $bucket = null)
    {
        $zone = Zone::zonez1(); //华北

        $config = new Config($zone);
        $bucket = empty($bucket) ? $this->defaultBucket : $bucket;
        $bucketManager = new BucketManager($this->auth, $config);
        $error = $bucketManager->delete($bucket, $key);
        // if($error){
            // Yii::$app->response->error(
                // Yii::$app->response->resCode['ERROR_QINIU_DELETE_FAILED'],
                // 'Delete files from qiniu failed.'
            // );
        // }
    }

    /**
     * 生成七牛 CDN URL
     *
     * @param string $key 相对路径
     *
     * @return string
     */
    public function generateUrl($key, $hostType = 'default')
    {
        return !empty($key) ? $this->cdnHost[$hostType] . trim($key, '/') : '';
    }

    /**
     * 去掉上传连接前缀
     *
     * @param string $url 绝对路径
     *
     * @return string
     */
    public function removeCdnHost($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        return $path ? substr($path, 1) : '';
    }

    /**
     * 为私有文件生成下载地址
     *
     * @param string $url 绝对路径
     *
     * @return string
     */
    public function generatePriDownloadUrl($url)
    {
        // 对链接进行签名
        $signedUrl = $this->auth->privateDownloadUrl($url);
        return $signedUrl ? : '';
    }
}
