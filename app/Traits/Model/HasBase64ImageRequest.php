<?php

namespace App\Traits\Model;

use App\Casts\Base64Image;
use App\Services\UploadMedia\Drivers\UploadBase64ImageDriver;
use App\Services\UploadMedia\UploadMediaService;
use Illuminate\Database\Eloquent\Model;

trait HasBase64ImageRequest
{
    public static function bootHasBase64ImageRequest(): void
    {
        $mediaUploaderService = new UploadMediaService(new UploadBase64ImageDriver(), Base64Image::class);

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
