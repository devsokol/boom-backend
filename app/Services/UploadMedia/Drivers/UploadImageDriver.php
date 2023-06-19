<?php

namespace App\Services\UploadMedia\Drivers;

use App\Helpers\StorageHelper;
use App\Services\UploadMedia\UploadMediaService;
use App\Utils\FilenameSanitizer;
use App\Utils\FileUtility;
use App\Utils\ImageUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadImageDriver extends AbstractUploadDriver
{
    private string $disk;

    public function __construct(
        private string $extension = 'jpg',
        private int $quality = 75,
        private int $maxWidth = 1800,
        private int $maxHeight = 1800,
        private bool $isOriginalName = true,
        ?string $subfolder = null,
    ) {
        $this->setSubfolder($subfolder);

        $this->disk = UploadMediaService::defaultDisk();
    }

    public function amountOfParamsInCast(): int
    {
        return 6;
    }

    public function store(string $attribute, Model $model, array $params): ?string
    {
        if (! empty($params)) {
            $this->parseParams($params);
        }

        if ($model->{$attribute} instanceof \Illuminate\Http\UploadedFile) {
            $this->deletePrevImage($model);

            $definedPath = $this->definePathToSaveContent($this->subfolder);

            if ($this->isOriginalName) {
                $originalName = pathinfo($model->{$attribute}->getClientOriginalName(), PATHINFO_FILENAME);

                $originalName = FilenameSanitizer::sanitize($originalName . '.' . $this->extension);

                $originalName = FileUtility::trimFilename($originalName);
            } else {
                $originalName = $this->generateRandomName();
            }

            $compressedImage = $this->compressSavedImage($model->{$attribute});

            $originalName = StorageHelper::preventDuplicate($definedPath . DIRECTORY_SEPARATOR . $originalName, true);

            return $compressedImage->storeAs($definedPath, $originalName);
        }

        $this->deletePrevImageIfRequestNull($model, $attribute, $model->{$attribute});

        return null;
    }

    public function getPath(?string $rawPath): ?string
    {
        return ! empty($rawPath) ? Storage::disk($this->disk)->url($rawPath) : '';
    }

    public function delete(?string $rawOriginal): void
    {
        $this->deleteImage($rawOriginal);
    }

    private function parseParams(array $params): void
    {
        [$ext, $quality, $maxWidth, $maxHeight, $subfolder, $isOriginalName] = $params;

        if ($ext) {
            $this->extension = $ext;
        }

        if ($quality) {
            $this->quality = $quality;
        }

        if ($maxWidth) {
            $this->maxWidth = $maxWidth;
        }

        if ($maxHeight) {
            $this->maxHeight = $maxHeight;
        }

        if ($isOriginalName) {
            $this->isOriginalName = $isOriginalName;
        }

        $this->subfolder = $subfolder;
    }

    private function generateRandomName(): string
    {
        return Str::random(40) . '.' . $this->extension;
    }

    private function deletePrevImageIfRequestNull(Model $model, string $attribute, ?string $field): void
    {
        if (is_null($field) && $model->exists) {
            $this->deleteImage($model->getRawOriginal($attribute));
        }
    }

    private function deletePrevImage(string $attributeName): void
    {
        $this->deleteImage($this->getActualAttribute($attributeName));
    }

    private function deleteImage(?string $rawOriginal): void
    {
        if ($rawOriginal) {
            Storage::disk($this->disk)->delete($rawOriginal);
        }
    }

    public function compressSavedImage(UploadedFile $image): UploadedFile
    {
        $imageUtility = new ImageUtility($this->maxWidth, $this->maxHeight, $this->quality, $this->extension);

        return $imageUtility->compressSavedImage($image);
    }
}
