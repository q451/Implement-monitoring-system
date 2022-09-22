<?php
/**
 * module/service层公共类
 * 所有提供service服务的类要继承此类
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-24 11:29:04
 * @update mazhenling
 */

namespace app\common\base;

use Yii;
use yii\db\Expression;
use yii\base\Component;
use app\common\behaviors\ApiCommonBehavior;

class ApiService extends Component
{
    public $model;
    public $timeNow;

    public function behaviors()
    {
        return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'commonBehavior' => [
                'class' => ApiCommonBehavior::className(),
            ],
        ]);
    }

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
    public function add($attributes)
    {
        return $this->model->zdAdd($attributes);
    }

    /**
     * 删除一条数据
     *
     * @param integer|array $condition 删除数据的 ID ，或者是一个条件数组
     * WARING: 如果是条件数组，则要确保数据的唯一性
     *
     * @return null
     */
    public function delete($condition)
    {
        $this->model->zdDelete($condition);
    }

    /**
     * 按条件批量删除数据
     *
     * @param array $condition 删除的条件
     *
     * @return null
     */
    public function deleteAll($condition)
    {
        if (!is_array($condition) || empty($condition)) {
            self::error(
                'ERROR_NO_CONDITION_DELETE',
                'No conditions when delete.'
            );
        }

        $this->model->zdDeleteAll($condition);
    }

    /**
     * 按照sql语句更新数据
     *
     * @param array $attributes 要更新的数据
     * @param array $condition 更新的条件
     *
     * @return null
     */
    public function updateByExpression($attributes, $condition = [])
    {
        $attributesUpdate = [];
        foreach($attributes as $key => $value) {
            $attributesUpdate[$key] = new Expression((string)$value);
        }
        $command = Yii::$app->db->createCommand();
        $command->update($this->model->tableName(),$attributesUpdate,$condition )
                ->rawSql.';';
        $command->execute();
        return $command->rawSql;
    }

    /**
     * 更新一条数据
     *
     * @param array $attributes 要更新的数据，如果包含 ID 则以 ID 为条件进行更新操作
     * @param array $condition 更新的条件，仅在更新的数据不包含 ID 的情况下有效
     *
     * @return null
     */
    public function update($attributes, $condition = [])
    {
        $id = $this->model->zdUpdate($attributes, $condition);
        return $id;
    }

    /**
     * 按条件批量更新数据
     *
     * @param array $attributes 要更新的数据
     * @param array $condition 更新的条件
     */
    public function updateAll($attributes, $condition)
    {
        if (!is_array($condition) || empty($condition)) {
            self::error(
                'ERROR_NO_CONDITION_UPDATE',
                'No conditions when update.'
            );
        }

        $this->model->zdUpdateAll($attributes, $condition);
    }

    /**
     * 按条件批量更新数据 , 且不触发behavior
     *
     * @param array $attributes 要更新的数据
     * @param array $condition 更新的条件
     */
    public function updateAllNotBehavior($attributes, $condition)
    {
        if (!is_array($condition) || empty($condition)) {
            self::error(
                'ERROR_NO_CONDITION_UPDATE',
                'No conditions when update.'
            );
        }

        $this->model->zdUpdateAllNotBehavior($attributes, $condition);
    }

    /**
     * 获取一条数据
     *
     * @param array $args 参数数组，包含查询的各种限制条件：
     *      array $condition 查询条件
     *      string|array $with 关联查询
     *      boolean $isAsArray 是否以数组的形式返回结果
     *
     * @return object|array|null
     */
    public function info($args)
    {
        return $this->model->zdInfo($args);
    }

    /**
     * 根据 ID 获取一条数据
     *
     */
    public function infoById($id)
    {
        return $this->model->zdInfo([
            'condition' => ['id' => $id]
        ]);
    }

    /**
     * 获取数据列表
     *
     * @param array $args 参数数组，包含查询的各种限制条件：
     *      integer $page 分页的页码
     *      integer $size 查询数量
     *      array $condition 查询条件
     *      string $orderby 排序字段
     *      const $sort 顺序
     *      string|array $with 关联查询
     *      boolean $isAsArray 是否以数组的形式返回结果
     *
     * @return object|array|null
     */
    public function lists($args)
    {
        return $this->model->zdInfos($args);
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
    public function save($attributes, $uniqFields, $updateIgnoreFields = [])
    {
        return $this->model->zdSave($attributes, $uniqFields, $updateIgnoreFields);
    }

    /**
     * 保存多组数据
     *
     * @param array $attributes 多组数据组成的数组
     *
     * @return array 添加数据的ID组成的数组集合
     */
    public function saveMany($attributes, $uniqFields, $updateIgnoreFields = [])
    {
        $ids = array();
        foreach($attributes as $attribute)
        {
            $_model = clone $this->model;
            $ids[] = $_model->zdSave($attribute, $uniqFields, $updateIgnoreFields);
        }
		return $ids;
    }
	
	/**
     * 保存多组数据, 且不触发behavior
     *
     * @param array $attributes 多组数据组成的数组
     *
     * @return array 添加数据的ID组成的数组集合
     */
    public function saveManyNotBehavior($attributes, $uniqFields, $updateIgnoreFields = [])
    {
        $ids = array();
        foreach($attributes as $attribute)
        {
            $_model = clone $this->model;
            $ids[] = $_model->zdSaveNotBehavior($attribute, $uniqFields, $updateIgnoreFields);
        }
		return $ids;
    }

    /**
     * 添加多组数据
     *
     * @param array $attributes 多组数据组成的数组
     *
     * @return array 添加数据的ID组成的数组集合
     */
    public function addMany($attributes)
    {
        $ids = array();
        foreach($attributes as $attribute)
        {
            $_model = clone $this->model;
            $ids[] = $_model->zdAdd($attribute);
        }
        return $ids;
    }

    // 返回一定条件下总条数
    public function countInCondition($condition, $groupby='', $with = [], $filter = [])
    {
        $count = $this->model::find()
                      ->where($condition)
                      ->joinWith($with)
                      ->andFilterWhere($filter)
                      ->groupby($groupby)
                      ->count();
        return intval($count);
    }
}
