<?php

namespace App\Services\UploadMedia\Drivers;

use App\Helpers\ModelHelper;
use App\Helpers\StorageHelper;
use App\Services\UploadMedia\Contracts\IUploadDriver;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractUploadDriver implements IUploadDriver
{
    protected Model $model;

    protected ?string $subfolder = null;

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getSubfolder(): ?string
    {
        return $this->subfolder;
    }

    public function setSubfolder(?string $subfolder): self
    {
        $this->subfolder = $subfolder;

        return $this;
    }

    public function amountOfParamsInCast(): int
    {
        return 0;
    }

    public function optionalParamsInCastFilledAs(): mixed
    {
        return null;
    }

    public function getActualAttribute(string $field): mixed
    {
        return ModelHelper::getActualAttribute($this->model, $field);
    }

    protected function definePathToSaveContent(?string $subfolder = null): string
    {
        $anonymous = method_exists($this->model, 'anonymousFilePath')
            ? (bool) $this->model->anonymousFilePath()
            : false;

        $withDate = method_exists($this->model, 'filepathWithDate') ? (bool) $this->model->filepathWithDate() : false;

        return StorageHelper::definePathToSaveContent($this->model, $subfolder, $anonymous, $withDate);
    }
}
