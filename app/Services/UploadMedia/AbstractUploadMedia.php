<?php

namespace App\Services\UploadMedia;

use App\Helpers\ModelHelper;
use App\Services\UploadMedia\Contracts\IUploadDriver;
use Closure;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractUploadMedia
{
    protected Model $model;

    protected string $castClass;

    protected IUploadDriver $uploadDriver;

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setCastClass(string $castClass): self
    {
        $this->castClass = $castClass;

        return $this;
    }

    public function getUploadDriver(): IUploadDriver
    {
        return $this->uploadDriver;
    }

    public function setUploadDriver(IUploadDriver $iUploadDriver): self
    {
        $this->uploadDriver = $iUploadDriver;

        return $this;
    }

    public static function defaultDisk(): string
    {
        return config('filesystems.default');
    }

    /**
     * For example:
     * protected $casts = [
     *   'placeholder' => Base64Image::class . ':jpg,75,1800,1800,placeholders',
     * ];.
     */
    public function parseCastParamsByField(string $field): array
    {
        $casts = $this->model->getCasts();

        $params = explode(',', str_replace(':', '', strstr($casts[$field], ':')));

        $params = array_reduce($params, function ($carry, $p) {
            if ($p === 'true' || $p === 'false') {
                $p = filter_var($p, FILTER_VALIDATE_BOOLEAN);
            } elseif ($p === 'null') {
                $p = null;
            }

            $carry[] = $p;

            return $carry;
        }, []);

        $amountOfParamsInCast = $this->getUploadDriver()->amountOfParamsInCast();
        $optionalParamsInCastFilledAs = $this->getUploadDriver()->optionalParamsInCastFilledAs();

        return array_pad($params, $amountOfParamsInCast, $optionalParamsInCastFilledAs);
    }

    public function store(): void
    {
        $this->throughAllCastedAttributes(function ($field, $model) {
            if (! $model->exists && $model->isDirty($field)) {
                $params = $this->parseCastParamsByField($field);

                $path = $this->getUploadDriver()->store($field, $model, $params);

                $model->{$field} = $path;
            }
        });
    }

    public function update(): void
    {
        $this->throughAllCastedAttributes(function ($field, $model) {
            if ($model->exists && $model->isDirty($field)) {
                $params = $this->parseCastParamsByField($field);

                $rawOriginal = $model->getRawOriginal($field);

                $this->getUploadDriver()->delete($rawOriginal);

                $path = $this->getUploadDriver()->store($field, $model, $params);

                $model->{$field} = $path;
            }
        });
    }

    public function getPath(string $attribute): ?string
    {
        $field = ModelHelper::getActualAttribute($this->model, $attribute);

        if (empty($field)) {
            return null;
        }

        return $this->getUploadDriver()->getPath($field);
    }

    public function delete(): void
    {
        $this->throughAllCastedAttributes(function ($field, $model) {
            $rawOriginal = $model->getRawOriginal($field);

            if (empty($field)) {
                return;
            }

            $this->getUploadDriver()->delete($rawOriginal);
        });
    }

    public function throughAllCastedAttributes(Closure $closure): void
    {
        $attributes = collect($this->model->getCasts())->filter(function ($item) {
            if (strpos($item, ':') !== false) {
                // remove params like: :jpg,75,512,512,id,null,avatar
                return strstr($item, ':', true) === $this->castClass;
            }

            return $item === $this->castClass;
        })->keys();

        foreach ($attributes as $field) {
            $closure($field, $this->model);
        }
    }
}
