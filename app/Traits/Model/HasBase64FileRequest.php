<?php

namespace App\Traits\Model;

use App\Casts\Base64File;
use App\Services\UploadMedia\Drivers\UploadBase64FileDriver;
use App\Services\UploadMedia\UploadMediaService;
use Illuminate\Database\Eloquent\Model;

trait HasBase64FileRequest
{
    public static function bootHasBase64FileRequest(): void
    {
        $mediaUploaderService = new UploadMediaService(new UploadBase64FileDriver(), Base64File::class);

        static::saving(function (Model $model) use ($mediaUploaderService) {
            $mediaUploaderService->setModel($model)->store();
        });

        static::deleting(function (Model $model) use ($mediaUploaderService) {
            $mediaUploaderService->setModel($model)->delete();
        });

        static::updating(function (Model $model) use ($mediaUploaderService) {
            $mediaUploaderService->setModel($model)->update();
        });
    }

    public function anonymousFilePath(): bool
    {
        return false;
    }

    public function filepathWithDate(): bool
    {
        return false;
    }
}
