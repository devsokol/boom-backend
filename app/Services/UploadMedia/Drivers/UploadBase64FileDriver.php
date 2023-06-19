<?php

namespace App\Services\UploadMedia\Drivers;

use App\Services\UploadMedia\UploadMediaService;
use App\Utils\FileUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadBase64FileDriver extends AbstractUploadDriver
{
    private ?string $extension;

    private string $disk;

    public function __construct(?string $subfolder = null)
    {
        $this->setSubfolder($subfolder);

        $this->disk = UploadMediaService::defaultDisk();
    }

    public function amountOfParamsInCast(): int
    {
        return 1;
    }

    public function store(string $attribute, Model $model, array $params): ?string
    {
        if (! empty($params)) {
            $this->parseParams($params);
        }

        $file = $this->getActualAttribute($attribute);

        $this->deletePrevFileIfRequestNull($model, $attribute, $file);

        if ($file && $this->isBase64File($file)) {
            return $this->uploadBase64File($file, $attribute);
        }

        return null;
    }

    public function uploadBase64File(string $value, string $attributeName): string
    {
        $hashName = $this->generateRandomName($value);

        $definedPath = $this->definePathToSaveContent($this->getSubfolder());

        $this->storeStreamFileAndRemovePrevious($value, $attributeName, $hashName, $definedPath);

        return Str::replaceFirst('public' . DIRECTORY_SEPARATOR, '', $definedPath) . DIRECTORY_SEPARATOR . $hashName;
    }

    public function storeBase64FileAndRemovePrevious(
        string $stream,
        string $attributeName,
        string $filename,
        string $catalogName
    ): string {
        $destinationPath = ! empty($filename) ? $catalogName . DIRECTORY_SEPARATOR . $filename : $catalogName;

        $filepath = Storage::disk($this->disk)->put($destinationPath, base64_decode($stream));

        $this->deleteFile($this->getActualAttribute($attributeName));

        return $filepath;
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
        [$subfolder] = $params;

        $this->setSubfolder($subfolder);
    }

    private function generateRandomName(string $value): string
    {
        $fileUtility = app(FileUtility::class);

        $this->extension = $fileUtility->getExtensionFromStream($value);

        return Str::random(40) . '.' . (empty($this->extension) ?: '.' . $this->extension);
    }

    private function deletePrevFileIfRequestNull(Model $model, string $attribute, ?string $field): void
    {
        if (is_null($field) && $model->exists) {
            $this->deleteFile($model->getRawOriginal($attribute));
        }
    }

    private function deleteFile(?string $rawOriginal): void
    {
        if ($rawOriginal) {
            Storage::disk($this->disk)->delete($rawOriginal);
        }
    }

    private function isBase64File(string $payload): bool
    {
        return base64_decode($payload, true);
    }
}
