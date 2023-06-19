<?php

namespace App\Services\Support\Fractal\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasAutoFiller
{
    public function fillTransformer(?Model $model, array $attributes, bool $hideNullable = false): array
    {
        if (! $model) {
            return [];
        }

        $data = [];

        foreach ($attributes as $key => $val) {
            if (is_int($key) && isset($model->{$val})) {
                if ($hideNullable && is_null($model->{$val})) {
                    continue;
                }

                $data[$val] = $model->{$val};
            } elseif (is_string($key) && ! is_null($val)) {
                $data[$key] = $val;
            }
        }

        return $data;
    }
}
