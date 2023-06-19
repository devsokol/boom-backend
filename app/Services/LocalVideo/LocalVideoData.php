<?php

namespace App\Services\LocalVideo;

use Illuminate\Support\Facades\Storage;
use App\Services\UploadMedia\UploadMediaService;

class LocalVideoData
{
    private string $disk;

    private string $videoPath;

    private ?string $thumbnailPath;

    public function __construct(string $params)
    {
        [$videoPath, $thumbnailPath] = self::getParams($params);

        $this->videoPath = $videoPath;
        $this->thumbnailPath = $thumbnailPath;

        $this->disk = UploadMediaService::defaultDisk();
    }

    public function __toString()
    {
        return json_encode($this->getAssets(), JSON_UNESCAPED_SLASHES);
    }

    public static function getParams(string $params): array
    {
        $videoWithThumb = explode('~', substr($params, 4));

        if (count($videoWithThumb) !== 2) {
            $video = $videoWithThumb[0] ?? null;

            return [$video, null];
        }

        return $videoWithThumb;
    }

    public function getAssets(): array
    {
        return [
            'assets' => [
                'thumbnail' => $this->getThumbnail(),
                'mp4' => $this->getMp4(),
            ],
        ];
    }

    public function getThumbnail(): string
    {
        return ($this->thumbnailPath && ! empty($this->thumbnailPath))
            ? Storage::disk($this->disk)->url($this->thumbnailPath)
            : asset('/assets/images/no-content.png');
    }

    public function getMp4(): ?string
    {
        return $this->videoPath ? Storage::disk($this->disk)->url($this->videoPath) : '';
    }
}
