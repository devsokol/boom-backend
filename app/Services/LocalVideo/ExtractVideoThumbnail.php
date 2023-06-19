<?php

namespace App\Services\LocalVideo;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Coordinate;
use FFMpeg\Media\Video;
use App\Utils\ImageUtility;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\Storage;
use App\Services\UploadMedia\UploadMediaService;

class ExtractVideoThumbnail
{
    protected FFMpeg $ffmpeg;
    protected Video $video;
    
    public function __construct(protected string $videoUrl, private string $storeToFolder)
    {
    }

    public function create(): string
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => config('video-thumbnail.binaries.ffmpeg'),
            'ffprobe.binaries' => config('video-thumbnail.binaries.ffprobe'),
        ]);

        $this->video = $this->ffmpeg->open($this->videoUrl);

        $temporaryThumbnailPath = $this->generateTemporaryPath();

        $this->storeThumbnailToTemporary($temporaryThumbnailPath);

        $this->compressThumbnail($temporaryThumbnailPath);

        $destinationPath = $this->preparePath();

        Storage::disk($this->defaultDisk())->put($destinationPath, file_get_contents($temporaryThumbnailPath));

        return $destinationPath;
    }

    private function defaultDisk(): string
    {
        return UploadMediaService::defaultDisk();
    }

    private function generateTemporaryPath(): string
    {
        return sprintf('%s/%s', sys_get_temp_dir(), str()->random(40));
    }

    private function storeThumbnailToTemporary(string $temporaryPath): void
    {
        $randomSeconds = $this->getRandomSecondsBetweenDuration();

        $frame = $this->video->frame(Coordinate\TimeCode::fromSeconds($randomSeconds));

        $frame->save($temporaryPath);
    }

    private function getDuration(): int
    {
        $ffprobe = FFProbe::create([
            'ffmpeg.binaries'  => config('video-thumbnail.binaries.ffmpeg'),
            'ffprobe.binaries' => config('video-thumbnail.binaries.ffprobe'),
        ]);

        return intval($ffprobe->format($this->videoUrl)->get('duration'));
    }

    private function getRandomSecondsBetweenDuration(): int
    {
        return rand(1, $this->getDuration());
    }

    private function preparePath(): string
    {
        $pre = 'thumb-';

        return $this->storeToFolder . DIRECTORY_SEPARATOR . $pre . str()->random(40) . '.jpg';
    }

    private function compressThumbnail(string $path): void
    {
        (new ImageUtility(640, 480, 25, defaultFit: Manipulations::FIT_FILL))->compressSavedImage($path);
    }
}
