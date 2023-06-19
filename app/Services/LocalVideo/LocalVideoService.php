<?php

namespace App\Services\LocalVideo;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\LocalVideo\LocalVideoData;
use App\Services\UploadMedia\UploadMediaService;
use Exception;

class LocalVideoService
{
    private string $disk;

    public function __construct()
    {
        $this->disk = UploadMediaService::defaultDisk();
    }

    public function store(UploadedFile $file, string $definedPath): ?string
    {
        $thumbnailPath = '';

        try {
            $thumbnailPath = $this->extractThumbnailFromVideoByFile($file, $definedPath);
        } catch (Exception $e) {
        }

        $filename = str()->random(40) . '.' . $file->getClientOriginalExtension();

        $videoPath = $file->storeAs($definedPath, $filename);

        return 'lvs:' . $videoPath . '~' . $thumbnailPath;
    }

    public function delete(string $params): void
    {
        [$videoPath, $thumbnailPath] = LocalVideoData::getParams($params);

        $this->deleteFile($videoPath);
        $this->deleteFile($thumbnailPath);
    }

    private function extractThumbnailFromVideoByFile(UploadedFile $file, string $definedPath): string
    {
        $tmpFile = $file->path();

        $extractThumbnail = new ExtractVideoThumbnail($tmpFile, $definedPath);

        return $extractThumbnail->create();
    }

    private function deleteFile(?string $rawOriginal): void
    {
        if ($rawOriginal) {
            Storage::disk($this->disk)->delete($rawOriginal);
        }
    }
}
