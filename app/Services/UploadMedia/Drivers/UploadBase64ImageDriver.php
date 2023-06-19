<?php

namespace App\Services\UploadMedia\Drivers;

use App\Exceptions\UnsupportedFormatException;
use App\Services\UploadMedia\UploadMediaService;
use App\Utils\ImageUtility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadBase64ImageDriver extends AbstractUploadDriver
{
    private string $disk;

    public function __construct(
        private string $extension = 'jpg',
        private int $quality = 75,
        private int $maxWidth = 1800,
        private int $maxHeight = 1800,
        ?string $subfolder = null,
    ) {
        $this->setSubfolder($subfolder);

        $this->disk = UploadMediaService::defaultDisk();
    }

    public function amountOfParamsInCast(): int
    {
        return 5;
    }

    public function store(string $attribute, Model $model, array $params): ?string
    {
        if (! empty($params)) {
            $this->parseParams($params);
        }

        $file = $this->getActualAttribute($attribute);

        $this->deletePrevImageIfRequestNull($model, $attribute, $file);

        if ($file && $this->isBase64Image($file)) {
            return $this->uploadBase64Image($model, $file, $attribute);
        }

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
        [$ext, $quality, $maxWidth, $maxHeight, $subfolder] = $params;

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

        $this->setSubfolder($subfolder);
    }

    private function uploadBase64Image(Model $model, string $value, string $attribute): string
    {
        if (! $this->isBase64Image($value)) {
            throw new UnsupportedFormatException('Unsupported format while upload image base64');
        }

        $hashName = $this->generateRandomName();

        $definedPath = $this->definePathToSaveContent($this->getSubfolder());

        $image = $this->createTemporaryImageFromBase64Image($value);

        $compressedImage = $this->compressSavedImage($image);

        $path = $compressedImage->storeAs($definedPath, $hashName);

        $this->deletePrevImageIfRequestNull($model, $attribute, $model->{$attribute});

        return $path;
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

    private function createTemporaryImageFromBase64Image(string $value): UploadedFile
    {
        $ImageUtility = new ImageUtility();

        $parsedExtension = ImageUtility::getImageExtensionFromBase64($value);

        if ($parsedExtension === 'png') {
            $this->extension = $parsedExtension;
        }

        $ImageUtility->setFormat($this->extension);

        return $ImageUtility->createTemporaryImageFileFromBase64($value);
    }

    public function compressSavedImage(UploadedFile $image): UploadedFile
    {
        $imageUtility = new ImageUtility($this->maxWidth, $this->maxHeight, $this->quality, $this->extension);

        return $imageUtility->compressSavedImage($image);
    }

    private function deleteImage(?string $rawOriginal): void
    {
        if ($rawOriginal) {
            Storage::disk($this->disk)->delete($rawOriginal);
        }
    }

    private function isBase64Image(string $payload): bool
    {
        return Str::startsWith($payload, 'data:image');
    }
}
