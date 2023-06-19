<?php

namespace App\Casts;

use App\Exceptions\TraitNotFoundException;
use App\Services\UploadMedia\UploadMediaService;
use App\Traits\Model\HasBase64ImageRequest;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Storage;

class Base64Image implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, $key, $value, $attributes): string
    {
        $disk = UploadMediaService::defaultDisk();

        return ! empty($value) ? Storage::disk($disk)->url($value) : '';
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if (! isExistsTraitInClass(HasBase64ImageRequest::class, $model)) {
            throw new TraitNotFoundException(sprintf(
                'Trait [%s] is not found in model: %s',
                HasBase64ImageRequest::class,
                get_class($model)
            ));
        }

        return $value;
    }
}
