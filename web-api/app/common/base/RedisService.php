<?php
/**
 * redis service 服务类
 *
 */

namespace app\common\base;

use Yii;
use yii\base\Component;
use app\common\behaviors\ApiCommonBehavior;

class RedisService extends Component
{
    public $redis;

    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'commonBehavior' => [
                'class' => ApiCommonBehavior::className(),
            ]
        ]);
    }

    public function init()
    {
        parent::init();
        $this->redis = Yii::$app->redis;
    }

    /**
     * 生成 key
     */
    public function genKey($index, $params = [])
    {
        return $this->redis->genKey($index, $params);
    }

    /**
     * key 是否存在
     */
    public function keyExists($key)
    {
        return $this->redis->execute('EXISTS', [$key]);
    }

    /**
     * get 一个存储对象的值
     */
    public function getItem($keyInfo)
    {
        return $this->redis->execute('GET', [$keyInfo['key']]);
    }

    /**
     * set 一个存储对象
     */
    public function setItem($keyInfo, $value)
    {
        $this->redis->execute('SET', [$keyInfo['key'], $value], $keyInfo);
    }

    /**
     * 初始化一个 list 缓存
     *
     * @param array $values 缓存元素数组，例如一系列 ID
     * @param array $keyInfo 缓存的 key 信息
     * @param enum $direct 入栈顺序 left|right
     *
     * @return Boolean
     */
    public function initList($values, $keyInfo, $direct = 'right')
    {
        $result = false;
        $key = $keyInfo['key'];
        if (!$this->keyExists($key) && !empty($values)) {
            array_unshift($values, $key);
            $command = $direct == 'left' ? 'LPUSH' : 'RPUSH';
            $result = $this->redis->execute($command, $values, $keyInfo);
        }
        return $result;
    }

    /**
     * 为 list 增加一个元素
     *
     * @param string|integer $value 要缓存的元素
     * @param array $keyInfo 缓存的 key 信息
     * @param enum $direct 入栈顺序 left|right
     *
     * @return Boolean
     */
    public function appendToList($value, $keyInfo, $direct = 'left')
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = $direct == 'left' ? 'LPUSH' : 'RPUSH';
            $result = $this->redis->execute($command, [$key, $value]);
        }
        return $result;
    }

    /**
     * 从 list 里获取指定区间的元素
     *
     * @param array $keyInfo 缓存的 key 信息
     * @param integer $start 区间起始位置
     * @param integer $stop 区间截止位置
     *
     * @return array
     */
    public function rangeList($keyInfo, $start, $stop)
    {
        $result = [];
        $key = $keyInfo['key'];
        if ($this->redis->execute('EXISTS', [$key])) {
            $result = $this->redis->execute('LRANGE', [$key, $start, $stop]);
        }
        return (array) $result;
    }

    /**
     * 为 list 移除指定的一个元素
     *
     * @param string|integer $value 要移除的元素
     * @param array $keyInfo 缓存的 key 信息
     * @param enum $count 移除个数  < 0 : 从尾往头 | 0 : 所有 | > 0 : 从头往尾
     *
     * @return Boolean
     */
    public function deleteOneList($value, $keyInfo, $count = 0)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'LREM';
            $result = $this->redis->execute($command, [$key, $count, $value]);
        }
        return $result;
    }

    /**
     * pop list 的一个元素
     *
     * @param string|integer $value 要移除的元素
     * @param array $keyInfo 缓存的 key 信息
     * @param enum $count 移除个数  < 0 : 从尾往头 | 0 : 所有 | > 0 : 从头往尾
     *
     * @return Boolean
     */
    public function popOneList($keyInfo, $direct = 'left')
    {
        $result = 0;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = $direct == 'left' ? 'LPOP' : 'RPOP';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }

    /**
     * 获取 list 的长度
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function getListLength($keyInfo)
    {
        $result = 0;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'LLEN';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }

    /**
     * 初始化一个 sorted set 缓存
     *
     * @param array $values 缓存成员数组，score member
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function initSortedSet($values, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if (!$this->keyExists($key) && !empty($values)) {
            array_unshift($values, $key);
            $command = 'ZADD';
            $result = $this->redis->execute($command, $values, $keyInfo);
        }
        return $result;
    }

    /**
     * 为 sorted set 添加一组数据
     *
     * @param array $values 缓存成员数组,结构 score member
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function appendToSortedSet($values, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            array_unshift($values, $key);
            $command = 'ZADD';
            $result = $this->redis->execute($command, $values);
        }
        return $result;
    }

    /**
     * 返回sorted set中指定score 区间的一组数据(包含区间)
     *
     * @param array $keyInfo 缓存的 key 信息
     * @param integer $min 最小score
     * @param integer $max 最大score
     *
     * @return array
     */
    public function rangeByScoreSortedSet($keyInfo, $min, $max)
    {
        $result = [];
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'ZRANGEBYSCORE';
            $result = $this->redis->execute($command, [$key, $min, $max]);
        }
        return $result;
    }

    /**
     * sorted set中指定成员的分值自增
     *
     * @param array $keyInfo 缓存的 key 信息
     * @param string|integer $member 要改变的成员
     * @param integer $increment 增量
     *
     * @return boolean
     */
    public function incrementScoreToSortedSet($increment, $member, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'ZINCRBY';
            $result = $this->redis->execute($command, [$key, $increment, $member]);
        }
        return $result;
    }

    /**
     * 删除sorted set 中指定成员
     *
     * @param array $keyInfo 缓存的 key 信息
     * @param string|integer $member 要删除的成员
     *
     * @return boolean
     */
    public function removeMemberToSortedSet($member, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'ZREM';
            $result = $this->redis->execute($command, [$key, $member]);
        }
        return $result;
    }

    /**
     * 获取sorted set 中指定成员的score
     *
     * @param array $keyInfo 缓存的 key 信息
     * @param string | integer $member
     *
     * @return boolean
     */
    public function getMemberScoreToSortedSet($member, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'ZSCORE';
            $result = $this->redis->execute($command, [$key, $member]);
        }
        return $result;
    }

    /**
     * 获取sorted set 中指定成员的rank ，按照分数由高到低
     *
     * @param array $keyInfo 缓存的 key 信息
     * @param string | integer $member
     *
     * @return integer
     */
    public function getRevRankToSortedSet($member, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'ZREVRANK';
            $result = $this->redis->execute($command, [$key, $member]);
        }
        return $result;
    }

    /**
     * 获取sorted set 中按照分数由高到低的指定区间
     *
     * @param array $keyInfo 缓存的 key 信息
     * @param integer $start 区间起始位置
     * @param integer $stop 区间截止位置
     *
     * @return integer
     */
    public function getRevRangeToSortedSet($keyInfo, $start, $stop)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'ZREVRANGE';
            $result = $this->redis->execute($command, [$key, $start, $stop]);
        }
        return $result;
    }

    /**
     * 获取sorted set 的长度
     *
     * @param array $keyInfo 缓存的 key 信息
     * @return integer
     */
    public function getSortedSetLen($keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'ZCARD';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }

    /**
     * 初始化hashes 缓存
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function hSetInit($values, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if (!$this->keyExists($key) && !empty($values)) {
            array_unshift($values, $key);
            $command = 'HMSET';
            $result = $this->redis->execute($command, $values, $keyInfo);
        }
        return $result;
    }

    /**
     * 得到哈希集中所有字段的值
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function hGetAll($key)
    {
        $result = [];
        if ($this->keyExists($key)) {
            $command = 'HGETALL';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }

    /**
     * 得到哈希集中某个字段的值
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function hGet($key, $field)
    {
        $result = '';
        if ($this->keyExists($key)) {
            $command = 'HGET';
            $result = $this->redis->execute($command, [$key, $field]);
        }
        return $result;
    }

    /**
     * 改变哈希集中对应字段的值
     *
     * @param string $field 要改变的字段
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function hSet($key, $field, $value)
    {
        $command = 'HSET';
        $result = $this->redis->execute($command, [$key, $field, $value]);
        return $result;
    }

    /**
     * 为key 设置一个expire time
     *
     * @param interge $expire 过期时间
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return boolean
     */
    public function setExpireTimeToKey($expire, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'EXPIRE';
            $result = $this->redis->execute($command, [$key, $expire]);
            Yii::warning(sprintf('expire command key[%s] expire[%s] timestamp[%s]', $key, $expire, time()));
        }
        return $result;
    }

    /**
     * 删除某key
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return boolean
     */
    public function delKey($keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'DEL';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }

    /**
     * 查询某key距离过期剩余的时间
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return boolean
     */
    public function ttlKey($keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'TTL';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }

    /**
     * 初始化set 缓存
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function initSet($values, $keyInfo)
    {
        $result = false;
        $key = $keyInfo['key'];
        if (!empty($values)) {
            array_unshift($values, $key);
            $command = 'SADD';
            $result = $this->redis->execute($command, $values);
        }
        return $result;
    }

    /**
     * 获取set 元素数量
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function getSetLength($keyInfo)
    {
        $result = 0;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'SCARD';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }

    /**
     * pop set 一个元素
     *
     * @param array $keyInfo 缓存的 key 信息
     *
     * @return Boolean
     */
    public function popSet($keyInfo)
    {
        $result = 0;
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'SPOP';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }

    /**
     * 返回 set 中所有元素
     */
    public function membersSet($keyInfo)
    {
        $result = [];
        $key = $keyInfo['key'];
        if ($this->keyExists($key)) {
            $command = 'SMEMBERS';
            $result = $this->redis->execute($command, [$key]);
        }
        return $result;
    }
}
