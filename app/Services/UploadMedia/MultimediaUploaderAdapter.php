<?php

namespace App\Services\UploadMedia;

use App\Helpers\ModelHelper;
use Illuminate\Database\Eloquent\Model;
use App\Services\UploadMedia\Drivers\UploadFileDriver;
use App\Services\UploadMedia\Drivers\UploadImageDriver;
use App\Services\UploadMedia\Drivers\UploadLocalVideoDriver;

final class MultimediaUploaderAdapter extends AbstractUploadMedia
{
    private ?string $subfolder;

    private string $driverLabel;

    private array $definedDrivers;

    public function __construct(string $castClass, ?string $subfolder = null)
    {
        $this->subfolder = $subfolder;

        $this->definedDrivers();

        $this->setCastClass($castClass);
    }

    public function definedDrivers(): void
    {
        $this->definedDrivers = [
            'image' => [
                UploadImageDriver::class => [
                    'png', 'jpg', 'jpeg', 'bmp', 'gif', 'heic', 'heif', 'webp', 'avif',
                ],
            ],
            'lvs_video' => [
                UploadLocalVideoDriver::class => [
                    'mov', 'mp4', '3gp',
                ],
            ],
            'file' => [ //default
                UploadFileDriver::class => ['*'],
            ],
        ];
    }

    public function store(): void
    {
        $this->throughAllCastedAttributes(function ($field, $model) {
            if (! $model->exists && $model->isDirty($field)) {
                $this->detectDriver($model, $field);

                $path = $this->getUploadDriver()->store($field, $model, []);

                $model->{$field} = sprintf('%s|%s', $this->driverLabel, $path);
            }
        });
    }

    public function update(): void
    {
        $this->throughAllCastedAttributes(function ($field, $model) {
            if ($model->exists && $model->isDirty($field)) {
                $rawOriginal = $model->getRawOriginal($field);

                [$driverLabel, $path] = explode('|', $rawOriginal);

                $this->initUploadDriverByLabel($driverLabel);

                $this->getUploadDriver()->delete($path);

                $this->detectDriver($model, $field);

                $path = $this->getUploadDriver()->store($field, $model, []);

                $model->{$field} = sprintf('%s|%s', $this->driverLabel, $path);
            }
        });
    }

    public function delete(): void
    {
        $this->throughAllCastedAttributes(function ($field, $model) {
            $rawOriginal = $model->getRawOriginal($field);

            if (empty($field)) {
                return;
            }

            [$driverLabel, $path] = explode('|', $rawOriginal);

            $this->initUploadDriverByLabel($driverLabel);

            $this->getUploadDriver()->delete($path);
        });
    }

    public function getPath(string $attribute): ?string
    {
        $field = ModelHelper::getActualAttribute($this->model, $attribute);

        if (empty($field)) {
            return null;
        }

        [$driverLabel, $path] = explode('|', $field);

        $this->initUploadDriverByLabel($driverLabel);

        return $this->getUploadDriver()->getPath($path);
    }

    public function detectDriver(Model $model, string $field): void
    {
        $file = ModelHelper::getActualAttribute($model, $field);

        $extension = null;

        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $extension = strtolower($file->getClientOriginalExtension());
        }

        foreach ($this->definedDrivers as $label => $drivers) {
            foreach ($drivers as $driver => $supportedExtensions) {
                if (in_array($extension, $supportedExtensions) || current($supportedExtensions) === '*') {
                    $uploadDriver = app($driver)->setModel($model)->setSubfolder($this->subfolder);

                    $this->driverLabel = $label;

                    $this->setUploadDriver($uploadDriver);

                    break 2;
                }
            }
        }
    }

    private function initUploadDriverByLabel(string $label): void
    {
        if (isset($this->definedDrivers[$label])) {
            $class = current(array_keys($this->definedDrivers[$label]));

            $this->driverLabel = $label;

            $this->uploadDriver = app($class)->setModel($this->model)->setSubfolder($this->subfolder);

            return;
        }

        $this->driverLabel = 'file';

        $this->uploadDriver = app(UploadFileDriver::class)->setModel($this->model)->setSubfolder($this->subfolder);
    }
}
