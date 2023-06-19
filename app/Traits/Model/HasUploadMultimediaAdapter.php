<?php

namespace App\Traits\Model;

use App\Casts\Multimedia;
use App\Services\UploadMedia\MultimediaUploaderAdapter;
use Illuminate\Database\Eloquent\Model;

trait HasUploadMultimediaAdapter
{
    public static function bootHasUploadMultimediaAdapter(): void
    {
        $mediaUploaderService = new MultimediaUploaderAdapter(Multimedia::class);

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
