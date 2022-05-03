<?php

namespace yii\graphql\traits;

use yii\base\DynamicModel;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Trait ShouldValidate
 *
 *
 * @package yii\graphql\traits
 */
trait ShouldValidate
{

    protected function getResolver()
    {
        $resolver = parent::getResolver();
        if (!$resolver) {
            return null;
        }

        return function () use ($resolver) {
            $arguments = func_get_args();
            $args = ArrayHelper::getValue($arguments, 1, []);
            
            $data = [];
            foreach (array_keys($this->args()) as $attribute) {
                $data[$attribute] = isset($args[$attribute]) ? $args[$attribute] : null;
            }

            $model = DynamicModel::validateData($data, $this->rules());

            if ($model->hasErrors()) {
                $error = $model->getFirstErrors();
                $msg = 'input argument(' . key($error) . ') has validate error:' . reset($error);
                throw new InvalidParamException($msg);
            }

            return $resolver(...$arguments);
        };
    }
}
