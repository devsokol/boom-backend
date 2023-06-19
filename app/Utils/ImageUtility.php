<?php

namespace App\Utils;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic as InterventionImage;
use Spatie\Image\Image as SpatieImage;
use Spatie\Image\Manipulations;

class ImageUtility
{
    public function __construct(
        private int $maxWidth = 3000,
        private int $maxHeight = 3000,
        private int $imageQuality = 90,
        private string $defaultFormat = Manipulations::FORMAT_JPG,
        private string $defaultFit = Manipulations::FIT_MAX
    ) {
    }

    public function setMaxWidth(int $maxWidth): self
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function setMaxHeight(int $maxHeight): self
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    public function setMaxQuality(int $quality): self
    {
        $this->imageQuality = $quality;

        return $this;
    }

    public function setFormat(string $format): self
    {
        $this->defaultFormat = $format;

        return $this;
    }

    public function compressSavedImage(string|UploadedFile $image): string|UploadedFile
    {
        SpatieImage::load($image)
            ->useImageDriver(config('upload-media.image_driver'))
            ->fit($this->defaultFit, $this->maxWidth, $this->maxHeight)
            ->background('000000')
            ->quality($this->imageQuality)
            ->optimize()
            ->format($this->defaultFormat)
            ->save();

        return $image;
    }

    public function createFromStream(string $stream, ?int $maxWidth = null, ?int $maxHeight = null): Image
    {
        return InterventionImage::configure(['driver' => config('upload-media.image_driver')])
            ->make($stream)
            ->resize($maxWidth ?? $this->maxWidth, $maxHeight ?? $this->maxHeight, function ($constraint) {
                //$constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($this->defaultFormat, $this->imageQuality);
    }

    public function createTemporaryImageFileFromBase64(string $stream): UploadedFile
    {
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $stream);

        $image = str_replace(' ', '+', $image);

        $binary = base64_decode($image);

        $mimetype = self::getImageMimetypeFromBase64($stream);

        $extension = FileUtility::mime2ext($mimetype);

        $filename = Str::random(40) . '.' . $extension;

        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        file_put_contents($file, $binary);

        return new UploadedFile(
            $file,
            $filename,
            $mimetype,
            0,
            true
        );
    }

    public static function getImageMimetypeFromBase64(string $stream): string
    {
        $explode = explode(',', $stream);

        return str_replace(['data:', ';', 'base64'], ['', '', ''], $explode[0]);
    }

    public static function getImageExtensionFromBase64(string $stream): string
    {
        $explode = explode(',', $stream);

        return str_replace(['data:image/', ';', 'base64'], ['', '', ''], $explode[0]);
    }
}
