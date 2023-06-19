<?php

namespace App\Services;

use App\Services\AbstractService as ServicesAbstractService;

class ExtensionIconService extends ServicesAbstractService
{
    private string $definedFormat;

    private string $defaultIcon = 'default.svg';

    private string $pathToIcons = '/assets/icons/';

    private array $defaultIcons = [
        'video.svg' => [
            'mov', 'mp4', '3gp',
        ],
        'image.svg' => [
            'png', 'jpg', 'jpeg', 'bmp', 'gif',
        ],
        'default.svg' => [
            'pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'ods', 'csv', 'xls', 'xlsx',
        ],
    ];

    public function __toString()
    {
        return $this->selectIconRelativeToFormat();
    }

    public function __construct(?string $filename = null)
    {
        $this->detectExtensionFromFilename($filename);
    }

    public function detectExtensionFromFilename(?string $filename): void
    {
        $pathInfo = pathinfo($filename);

        $this->definedFormat = $pathInfo['extension'] ?? '';
    }

    public function selectIconRelativeToFormat(): string
    {
        foreach ($this->defaultIcons as $icon => $formats) {
            if (in_array($this->definedFormat, $formats)) {
                return $this->getUrl($icon);
            }
        }

        return $this->getUrl($this->defaultIcon);
    }

    private function getUrl(string $icon): string
    {
        return asset($this->pathToIcons . $icon);
    }
}
