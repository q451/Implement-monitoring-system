<?php
/**
 * API接口数据校验类
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-25 13:50:43
 */

namespace app\common\components;

use Yii;
use yii\base\DynamicModel;

class ApiValidator
{
    public $data           = []; // 需要验证的数据
    public $rules          = []; // 验证规则
    public $allFields      = []; // 所有的字段
    public $requiredFields = []; // 必须的字段
    public $validData      = []; // 验证后的数据

    public function __construct($data, $rules) {
        $this->data = $data;
        $this->rules = $rules;

        if ($message = $this->doValidate()) {
            Yii::$app->response->error(
                Yii::$app->response->resCode['ERROR_REQUEST_PARAMS'],
                $message
            );
        }
    }

    /**
     * 参数校验方法
     *
     * @return null
     */
    public function doValidate()
    {
        list($this->allFields, $this->requiredFields) = self::parseFields($this->rules);

        // 过滤非接口定义范围内的字段
        $unexpectedFields = array_diff(array_keys($this->data), $this->allFields);
        if (!empty($unexpectedFields)) {
            return ucfirst(implode(',', $unexpectedFields) . ' are(is) not expected');
        }

        // 把非必须参数映射到 $this->data
        foreach ($this->allFields as $field) {
            if (!isset($this->data[$field])) {
                $this->data[$field] = null;
            }
        }

        // 使用DynamicModel做其他规则校验
        $model = DynamicModel::validateData($this->data, $this->rules);
        if ($model->hasErrors()) {
            return $model->errors;
        }
        $this->validData = array_filter($model->attributes, function($val) {
            return $val !== null;
        });
        return null;
    }

    /**
     * 解析验证规则里面所有的字段，以及必须验证的字段
     *
     * @param $rules array 验证规则
     *
     * @return array
     */
    public static function parseFields($rules)
    {
        $allFields = $requiredFields = [];
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                $field = is_array($rule[0]) ? $rule[0] : [$rule[0]];
                $allFields = array_merge($allFields, $field);
                if (in_array('required', $rule, true)) {
                    $requiredFields = array_merge($requiredFields, $field);
                }
            }
        }
        return [
            array_unique($allFields),
            array_unique($requiredFields),
        ];
    }
}
