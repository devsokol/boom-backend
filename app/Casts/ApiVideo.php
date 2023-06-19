<?php

namespace App\Casts;

use App\Exceptions\TraitNotFoundException;
use App\Services\ApiVideo\ApiVideoData;
use App\Traits\Model\HasApiVideo;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ApiVideo implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return ApiVideoData|null
     */
    public function get($model, $key, $value, $attributes): ?ApiVideoData
    {
        return new ApiVideoData($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes): mixed
    {
        if (! isExistsTraitInClass(HasApiVideo::class, $model)) {
            throw new TraitNotFoundException(sprintf(
                'Trait [%s] is not found in model: %s',
                HasApiVideo::class,
                get_class($model)
            ));
        }

        return $value;
    }
}
