<?php

namespace App\Casts;

use App\Exceptions\TraitNotFoundException;
use App\Services\UploadMedia\MultimediaUploaderAdapter;
use App\Traits\Model\HasUploadMultimediaAdapter;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Multimedia implements CastsAttributes
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
    public function get($model, $key, $value, $attributes): ?string
    {
        $mediaUploaderService = new MultimediaUploaderAdapter(static::class);

        return $mediaUploaderService->setModel($model)->getPath($key);
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
        if (! isExistsTraitInClass(HasUploadMultimediaAdapter::class, $model)) {
            throw new TraitNotFoundException(sprintf(
                'Trait [%s] is not found in model: %s',
                HasUploadMultimediaAdapter::class,
                get_class($model)
            ));
        }

        return $value;
    }
}
