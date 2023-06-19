<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class ModelHelper
{
    public static function getActualAttribute(Model $model, string $attribute): mixed
    {
        $attributes = $model->getAttributes();

        return isset($attributes[$attribute]) ? $attributes[$attribute] : null;
    }
}
