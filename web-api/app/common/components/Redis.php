<?php
/**
 * 简单的封装 redis 方便以后扩展
 */

namespace app\common\components;

use Yii;
use yii\redis\Connection;
use yii\helpers\ArrayHelper;

class Redis extends Connection
{
    /**
     * redis server 列表
     * - 扩展支持根据不同的 key 来选择相应的 server
     * - 扩展支持集群，读写分离
     */
    public $servers = [];

    public $keySource = [];

    public function init()
    {
        parent::init();

        $server = $this->chooseServer();
    }

    /**
     * 根据当前环境选择一个 server
     */
    private function chooseServer()
    {
        $server = $this->servers[0];
        $this->hostname = $server['hostname'];
        $this->port     = $server['port'];
        $this->database = $server['database'];
        $this->password = $server['password'] ?? null;
    }

    /**
     * 根据配置文件的生成一个 redis key
     *
     * @param string $index 索引
     * @param array $params 参数
     *
     * @return array
     */
    public function genKey($index, $params = [])
    {
        $params[] = Yii::$app->language;
        $keyInfo = ArrayHelper::getValue($this->keySource, $index, false);
        if ($keyInfo) {
            $keyInfo['key'] = strtolower(vsprintf($keyInfo['key'], $params));
        }
        return $keyInfo;
    }

    /**
     * 执行 redis 命令
     *
     * @param string $name 命令名
     * @param array $params 命令参数
     * @param array|false $keyInfo 缓存的 key 信息
     *
     * @return null
     */
    public function execute($name, $params, $keyInfo = false) {
        $value = $this->executeCommand($name, $params);
        if (is_array($keyInfo) && $keyInfo['expire'] > 0) {
            $this->expire($keyInfo['key'], $keyInfo['expire']);
        }
        return $value;
    }
}
