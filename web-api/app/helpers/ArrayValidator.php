<?php
/**
 * 数组验证器
 *
 * @author heyijia
 * @date 2018-01-17
 */

namespace app\helpers;
use yii\validators\Validator;

class ArrayValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!is_array($model->$attribute)) {
            $this->addError($model, $attribute, $attribute . '必须是一个数组');
        }
    }
}

