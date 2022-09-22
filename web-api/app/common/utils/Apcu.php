<?php
namespace app\common\utils;

class Apcu {

    public function __construct() {
    }

    /**
     * @desc 获取缓存数据
     *
     * @param key
     * @return string
     */
    public function get($key) {
        $success = false;
        $stored = apcu_fetch($key, $success);
        if ( $success === false ) {
//            print_r("cache get false, key is ",$key);
            return false;
        }
        list($updateTime, $ttl, $data) = $stored;
        $now = time();
        if ($now < $updateTime || (($now - $updateTime) > $ttl)) {
//            print_r("cache $key found, but timeout ".($now - $updateTime)." out of ttl ",$ttl);
            return false;
        }
        return unserialize($data);
    }

    /**
     * @desc 设置缓存数据
     * @desc set ttl at put stage, has one benefit: cacher system can delete timeout entry automatically
     *
     * @param key
     * @param data
     * @param ttl
     * @ret bool
     */
    public function set($key, $data, $ttl = 300) {
        if (null === $data) {
//            print_r("put $key to cache, found invalid param, null data");
            return false;
        }
        return apcu_store (
            $key,
            array(time(), $ttl, serialize($data)),
            $ttl
        );
    }

    /**
     * @desc 删除缓存数据
     *
     * @param key
     */
    public function del($key) {
        $delRet = apcu_delete($key);
        if( $delRet != false ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 判断缓存是否存在
     *
     * @param key
     */
    public function exists($key) {
        $existsStatus = apcu_exists($key);
        if( $existsStatus != false ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 过去apcu内存使用情况
     *
     * @param key
     */
    public function statistic($limited = true) {
        $smaInfo = apcu_sma_info($limited);
        if( $smaInfo != false ){
            return $smaInfo;
        }else{
            return false;
        }
    }
}
