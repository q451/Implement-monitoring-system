<?php
/**
 * 公共的 Model 类
 * 所有的 Model 继承此类
 *
 * @author lunixy<lunixy@smartfinancelcoud.com>
 * @date 2017-06-07 10:37:34
 */

namespace app\common\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

class ApiModel extends ActiveRecord
{
    const SCENARIO_ADD = 'add';
    const SCENARIO_UPDATE = 'update';

    public $timeNow;
    public $excludeToArrayRelateAttr = ['langDict'];

    public function init()
    {
        parent::init();
        $this->timeNow = Yii::$app->params['timeNow'];
    }

    /**
     * 插入一条数据
     *
     * @apram array $attributes 要插入数据模型的属性
     *
     * @return integer|null
     */
    public function zdAdd($attributes)
    {
        if (array_key_exists($key = self::SCENARIO_ADD, $this->scenarios())) {
            $this->scenario = $key;
        }
        $this->attributes = $attributes;
        return $this->save() ? $this->id : null;
    }

    /**
     * 删除一条数据
     *
     * @param integer|array $condition 删除数据的 ID ，或者是一个条件数组
     * WARING: 如果是条件数组，则要确保数据的唯一性
     *
     * @return null
     */
    public function zdDelete($condition)
    {
        $obj = self::findOne($condition);
        if (!empty($obj)) {
            $obj->delete();
        }
    }

    /**
     * 按条件批量删除数据
     *
     * @param array $condition 删除的条件
     *
     * @return null
     */
    public function zdDeleteAll($condition)
    {
        self::deleteAll($condition);
    }

    /**
     * 更新一条数据
     *
     * @param array $attributes 要更新的数据，如果包含 ID 则以 ID 为条件进行更新操作
     * @param array $condition 更新的条件，仅在更新的数据不包含 ID 的情况下有效
     *
     * @return null
     */
    public function zdUpdate($attributes, $condition = [])
    {
        $obj = null;
        if (empty($condition)) {
            $id = isset($attributes['id']) ? $attributes['id'] : 0;
            $obj = self::findOne($id);
        } else {
            $obj = self::find()->where($condition)->one();
        }
        if (!empty($obj)) {
            if (array_key_exists($key = self::SCENARIO_UPDATE, $this->scenarios())) {
                $this->scenario = $key;
            }
            $obj->attributes = $attributes;
            $obj->update();
            return $obj->attributes['id'];
        }
    }

    /**
     * 按条件批量更新数据
     *
     * @param array $attributes 要更新的数据
     * @param array $condition 更新的条件
     */
    public function zdUpdateAll($attributes, $condition = [])
    {
        $obj = null;
        if (empty($condition)) {
            $id = isset($attributes['id']) ? $attributes['id'] : 0;
            $obj = self::findOne($id);
        } else {
            $obj = self::find()->where($condition)->all();
        }
        if (!empty($obj)) {
            if (array_key_exists($key = self::SCENARIO_UPDATE, $this->scenarios())) {
                $this->scenario = $key;
            }
            if(!is_array($obj)) {
                $obj->attributes = $attributes;
                $obj->update();
                return $obj->attributes['id'];
            }else {
                foreach($obj as $temp)
                {
                    $temp->attributes = $attributes;
                    $temp->update();
                }
                return $obj[0]->attributes['id'];
            }
        }
    }

	/**
     * 按条件批量更新数据 , 且不触发behavior
     *
     * @param array $attributes 要更新的数据
     * @param array $condition 更新的条件
     */
    public function zdUpdateAllNotBehavior($attributes, $condition = [])
    {
        $obj = null;
        if (empty($condition)) {
            $id = isset($attributes['id']) ? $attributes['id'] : 0;
            $obj = self::findOne($id);
        } else {
            $obj = self::find()->where($condition)->all();
        }
        if (!empty($obj)) {
            if (array_key_exists($key = self::SCENARIO_UPDATE, $this->scenarios())) {
                $this->scenario = $key;
            }
            if(!is_array($obj)) {
                $obj->attributes = $attributes;
                $obj->update();
                return $obj->attributes['id'];
            }else {
                self::updateAll($attributes, $condition);
                return $obj[0]->attributes['id'];
            }
        }
    }

    /**
     * 获取一条数据
     *
     * @param 同 function[zdInfos]
     *
     * @return object|array|null
     */
    public function zdInfo($args)
    {
        $args['size'] = 1;
        $objs = $this->zdInfos($args);
        return !empty($objs) ? $objs[0] : null;
    }

    /**
     * 获取数据列表
     *
     * @param array $args 参数数组，包含查询的各种限制条件：
     *      string|array    $fields    查询字段
     *      integer         $page      分页的页码
     *      integer|false   $size      查询数量
     *      array           $condition 查询条件
     *      string          $orderby   排序字段
     *      const           $sort      顺序
     *      string|array    $with      关联查询
     *      boolean         $isAsArray 是否以数组的形式返回结果
     *      string          $groupby   按组合字段
     *
     *      Notice: $condition 数组的格式
     *          1. ['id' => 1, 'name' => 'a'] 会转成 where id = 1 and name = "a"
     *          2. ['and', ['=', 'id', 1], ['like', 'name', 'a']] 会转成 where id = 1 and name like '%a%'
     *
     *          简单查询条件使用1，复杂查询条件使用2，参考 http://www.yiiframework.com/doc-2.0/yii-db-query.html#where()-detail
     *
     * @return object|array|null
     */
    public function zdInfos($args)
    {
        $fields    = '*';
        $page      = 1;
        $size      = false;
        $with      = [];
        $condition = [];
        $orderby   = [];
        $isAsArray = true;
        $groupby   = '';
        $having   = '';

        extract($args);

        $offset = $page > 1 ? ($page - 1) * $size : 0;

        $objs = self::find()
            ->select($fields)
            ->with($with)
            ->where($condition)
            ->orderby(!empty($orderby) ? $orderby : ['id' => SORT_DESC])
            ->groupby($groupby)
            ->having($having)
            ->offset($offset)
            ->limit($size)
            ->all();

        return ($isAsArray && !empty($objs)) ? $this->zdTranslateToArray($objs) : $objs;
    }

    /**
     * 递归遍历 ActiveRecord 中的 relatedRecords 并转成数组
     *
     * @param array|obj $obj 需要遍历的查询结果
     *
     * @return array
     */
    function zdTranslateToArray($obj)
    {
        $result = [];
        if (!empty($obj)) {
            // 处理 hasMany 的查询
            if (is_array($obj)) {
                foreach ($obj as $subObj) {
                    $result[] = $this->zdTranslateToArray($subObj);
                }
            } else {
                $result = $obj->toArray();
                $relations = $obj->getRelatedRecords();
                if (!empty($relations)) {
                    foreach ($relations as $key => $relation) {
                        if (!in_array($key, $this->excludeToArrayRelateAttr)) {
                            $result[$key] = $this->zdTranslateToArray($relation);
                        }
                    }
                }
           }
        }
        return $result;
    }

    /**
     * 保存一组数据，如果指定的字段组合已存在则更新，否则插入
     *
     * @param array $attributes 要保存的数据
     * @param array $uniqFields 唯一字段组合
     * @param array $updateIgnoreFields 做更新操作时忽略掉的字段（对插入操作无效）
     *
     * @return null
     */
    public function zdSave($attributes, $uniqFields, $updateIgnoreFields = [])
    {
        $condition = [];
        foreach ($uniqFields as $key) {
            $condition[$key] = $attributes[$key];
        }
        $obj = $this->zdInfos([
            'fields'    => 'id',
            'condition' => $condition,
        ]);
        if (!empty($obj)) {
            foreach ($updateIgnoreFields as $key) {
                unset($attributes[$key]);
            }
            $this->zdUpdateAll($attributes,$condition);
            $idList = ArrayHelper::getColumn($obj, 'id');
            if(count($obj) == 1)
                return $idList[0];
            return $idList;
        } else {
            return $this->zdAdd($attributes);
        }
    }

	/**
     * 保存一组数据，如果指定的字段组合已存在则更新，否则插入，并且不触发behavior
     *
     * @param array $attributes 要保存的数据
     * @param array $uniqFields 唯一字段组合
     * @param array $updateIgnoreFields 做更新操作时忽略掉的字段（对插入操作无效）
     *
     * @return null
     */
    public function zdSaveNotBehavior($attributes, $uniqFields, $updateIgnoreFields = [])
    {
        $condition = [];
        foreach ($uniqFields as $key) {
            $condition[$key] = $attributes[$key];
        }
        $obj = $this->zdInfos([
            'fields'    => 'id',
            'condition' => $condition,
        ]);
        if (!empty($obj)) {
            foreach ($updateIgnoreFields as $key) {
                unset($attributes[$key]);
            }
            self::updateAll($attributes, $condition); // self::updateAll($attributes, $condition);此方法不会触发behavior
            $idList = ArrayHelper::getColumn($obj, 'id');
            if(count($obj) == 1)
                return $idList[0];
            return $idList;
        } else {
            return $this->zdAdd($attributes);
        }
    }
}
