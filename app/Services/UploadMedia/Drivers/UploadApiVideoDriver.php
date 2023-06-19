<?php

namespace App\Services\UploadMedia\Drivers;

use App\Services\ApiVideo\ApiVideoData;
use App\Services\ApiVideo\ApiVideoService;
use Illuminate\Database\Eloquent\Model;

class UploadApiVideoDriver extends AbstractUploadDriver
{
    private ApiVideoService $apiVideoService;

    public function __construct()
    {
        $this->apiVideoService = new ApiVideoService();
    }

    public function store(string $fieldName, Model $model, array $params): ?string
    {
        $this->setModel($model);

        $file = $this->getActualAttribute($fieldName);

        return $this->apiVideoService->store($file);
    }

    public function getPath(?string $rawPath): ?string
    {
        return ! is_null($rawPath) ? (new ApiVideoData($rawPath))->getMp4() : '';
    }

    public function delete(?string $rawOriginal): void
    {
        if ($rawOriginal) {
            try {
                $this->apiVideoService->delete($rawOriginal);
            } catch (\Exception $e) {
            }
        }
    }
}
