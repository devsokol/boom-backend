<?php

namespace App\Observers;

use App\Helpers\ModelHelper;
use App\Helpers\StorageHelper;
use App\Utils\FilenameSanitizer;
use App\Utils\FileUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

abstract class AbstractObserver
{
    protected function uploadFile(Model &$model, string $attribute, ?string $subfolder = null): void
    {
        if ($model->exists) {
            $this->deletePrevFileByAttributeName($model, $attribute);
        }

        if ($model->{$attribute} instanceof \Illuminate\Http\UploadedFile) {
            $definedPath = $this->definePathToSaveContent($model, $subfolder);

            $originalName = $model->{$attribute}->getClientOriginalName();

            $originalName = FilenameSanitizer::sanitize($originalName);

            $originalName = FileUtility::trimFilename($originalName);

            $originalName = StorageHelper::preventDuplicate($definedPath . DIRECTORY_SEPARATOR . $originalName, true);

            $model->{$attribute} = $model->{$attribute}->storeAs($definedPath, $originalName);
        }
    }

    protected function deleteFileByAttributeName(Model $model, string $attributeName): void
    {
        $filepath = $this->getActualModelAttribute($model, $attributeName);

        if ($filepath) {
            $this->deleteFile($filepath);
        }
    }

    protected function deletePrevFileByAttributeName(Model $model, string $attributeName): void
    {
        $oldAttachment = $model->getRawOriginal($attributeName);

        $this->deleteFile($oldAttachment);
    }

    protected function deleteFile(?string $path): void
    {
        Storage::disk()->delete($path);
    }

    protected function getActualModelAttribute(Model $model, string $field): mixed
    {
        return ModelHelper::getActualAttribute($model, $field);
    }

    protected function definePathToSaveContent(Model $model, ?string $subfolder = null): string
    {
        return StorageHelper::definePathToSaveContent($model, $subfolder);
    }
}
