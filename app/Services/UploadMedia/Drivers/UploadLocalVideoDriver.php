<?php

namespace App\Services\UploadMedia\Drivers;

use Illuminate\Database\Eloquent\Model;
use App\Services\LocalVideo\LocalVideoData;
use App\Services\LocalVideo\LocalVideoService;
use Illuminate\Http\UploadedFile;

class UploadLocalVideoDriver extends AbstractUploadDriver
{
    private LocalVideoService $localVideoService;

    public function __construct()
    {
        $this->localVideoService = new LocalVideoService();
    }

    public function store(UploadedFile|string $value, Model $model, array $params): ?string
    {
        $this->setModel($model);

        $file = $value instanceof UploadedFile
            ? $value
            : $this->getActualAttribute($value);

        $definedPath = $this->definePathToSaveContent($this->subfolder);

        return $this->localVideoService->store($file, $definedPath);
    }

    public function getPath(?string $rawPath): ?string
    {
        return ! is_null($rawPath) ? (new LocalVideoData($rawPath))->getMp4() : '';
    }

    public function delete(?string $rawOriginal): void
    {
        if ($rawOriginal) {
            try {
                $this->localVideoService->delete($rawOriginal);
            } catch (\Exception $e) {
            }
        }
    }
}
