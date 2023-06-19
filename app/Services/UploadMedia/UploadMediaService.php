<?php

namespace App\Services\UploadMedia;

use App\Services\UploadMedia\Contracts\IUploadDriver;
use Illuminate\Database\Eloquent\Model;

class UploadMediaService extends AbstractUploadMedia
{
    public function __construct(IUploadDriver $uploadDriver, string $castClass)
    {
        $this->setUploadDriver($uploadDriver);

        $this->setCastClass($castClass);
    }

    public function setModel(Model $model): self
    {
        $this->model = $model;

        $this->uploadDriver->setModel($model);

        return $this;
    }
}
