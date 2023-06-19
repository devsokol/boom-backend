<?php

namespace App\Traits\Model;

use App\Casts\LocalVideo;
use Illuminate\Database\Eloquent\Model;
use App\Services\UploadMedia\UploadMediaService;
use App\Services\UploadMedia\Drivers\UploadLocalVideoDriver;

trait HasLocalVideo
{
    public static function bootHasLocalVideo(): void
    {
        $mediaUploaderService = new UploadMediaService(new UploadLocalVideoDriver(), LocalVideo::class);

        static::saving(function (Model $model) use ($mediaUploaderService) {
            $mediaUploaderService->setModel($model)->store();
        });

        static::deleting(function (Model $model) use ($mediaUploaderService) {
            $mediaUploaderService->setModel($model)->delete();
        });
    }
}
