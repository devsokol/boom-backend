<?php

namespace App\Services\FirebaseDynamicLinks;

class DynamicLinkResponse
{
    private ?string $link;

    private ?string $previewLink;

    private array $warnings = [];

    public function response(bool $returnOnlyLink = false): string | object
    {
        if ($returnOnlyLink) {
            return $this->link;
        }

        return (object) [
            'link' => $this->link,
            'preview_link' => $this->previewLink,
            'warnings' => $this->warnings,
        ];
    }

    public function setLink(string $link): DynamicLinkResponse
    {
        $this->link = $link;

        return $this;
    }

    public function setPreviewLink(string $previewLink): DynamicLinkResponse
    {
        $this->previewLink = $previewLink;

        return $this;
    }

    public function setWarnings(array $warnings): DynamicLinkResponse
    {
        $this->warnings = $warnings;

        return $this;
    }
}
