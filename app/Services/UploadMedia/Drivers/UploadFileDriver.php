<?php

namespace App\Services\UploadMedia\Drivers;

use App\Helpers\StorageHelper;
use App\Services\UploadMedia\UploadMediaService;
use App\Utils\FilenameSanitizer;
use App\Utils\FileUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileDriver extends AbstractUploadDriver
{
    private string $disk;

    private string $extension;

    public function __construct(
        private bool $isOriginalName = true,
        ?string $subfolder = null,
    ) {
        $this->setSubfolder($subfolder);

        $this->disk = UploadMediaService::defaultDisk();
    }

    public function amountOfParamsInCast(): int
    {
        return 2;
    }

    public function store(string $attribute, Model $model, array $params): ?string
    {
        if (! empty($params)) {
            $this->parseParams($params);
        }

        if ($model->{$attribute} instanceof \Illuminate\Http\UploadedFile) {
            $this->deletePrevFile($model);

            $definedPath = $this->definePathToSaveContent($this->subfolder);

            $this->extension = strtolower($model->{$attribute}->getClientOriginalExtension());

            if ($this->isOriginalName) {
                $originalName = $model->{$attribute}->getClientOriginalName();

                $originalName = FilenameSanitizer::sanitize($originalName);

                $originalName = FileUtility::trimFilename($originalName);
            } else {
                $originalName = $this->generateRandomName();
            }

            $originalName = StorageHelper::preventDuplicate($definedPath . DIRECTORY_SEPARATOR . $originalName, true);

            return $model->{$attribute}->storeAs($definedPath, $originalName);
        }

        $this->deletePrevFileIfRequestNull($model, $attribute, $model->{$attribute});

        return null;
    }

    public function getPath(?string $rawPath): ?string
    {
        return ! empty($rawPath) ? Storage::disk($this->disk)->url($rawPath) : '';
    }

    public function delete(?string $rawOriginal): void
    {
        $this->deleteFile($rawOriginal);
    }

    private function parseParams(array $params): void
    {
        [$isOriginalName, $subfolder] = $params;

        if ($isOriginalName) {
            $this->isOriginalName = $isOriginalName;
        }

        $this->subfolder = $subfolder;
    }

    private function generateRandomName(): string
    {
        return Str::random(40) . '.' . $this->extension;
    }

    private function deletePrevFileIfRequestNull(Model $model, string $attribute, ?string $field): void
    {
        if (is_null($field) && $model->exists) {
            $this->deleteFile($model->getRawOriginal($attribute));
        }
    }

    private function deletePrevFile(string $attributeName): void
    {
        $this->deleteFile($this->getActualAttribute($attributeName));
    }

    private function deleteFile(?string $rawOriginal): void
    {
        if ($rawOriginal) {
            Storage::disk($this->disk)->delete($rawOriginal);
        }
    }
}
