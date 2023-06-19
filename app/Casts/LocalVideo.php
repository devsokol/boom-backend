<?php

namespace App\Casts;

use App\Models\AttachmentType;
use App\Services\LocalVideo\LocalVideoData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Storage;

class LocalVideo implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return LocalVideoData|null
     */
    public function get($model, $key, $value, $attributes)
    {
        if (in_array($model->attachment_type_id, AttachmentType::getVideoTypesId())) {
            return new LocalVideoData($value);
        }

        if (AttachmentType::getType('link')->getKey() === $model->attachment_type_id) {
            return $value;
        }

        return Storage::url($value);
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
    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
