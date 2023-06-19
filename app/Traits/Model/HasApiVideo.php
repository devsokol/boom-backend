<?php

namespace App\Traits\Model;

use App\Casts\ApiVideo;
use App\Services\UploadMedia\Drivers\UploadApiVideoDriver;
use App\Services\UploadMedia\UploadMediaService;
use Illuminate\Database\Eloquent\Model;

trait HasApiVideo
{
    public static function bootHasApiVideo(): void
    {
        $mediaUploaderService = new UploadMediaService(new UploadApiVideoDriver(), ApiVideo::class);

        static::saving(function (Model $model) use ($mediaUploaderService) {
            $mediaUploaderService->setModel($model)->store();
        });

        static::deleting(function (Model $model) use ($mediaUploaderService) {
            $mediaUploaderService->setModel($model)->delete();
        });
    }
}
