<?php

namespace App\Services\FirebaseDynamicLinks;

use App\Services\FirebaseDynamicLinks\DynamicLinkResponse;
use App\Services\FirebaseDynamicLinks\Builders\ManualDynamicLinksGenerator;
use App\Services\FirebaseDynamicLinks\Builders\FirebaseDynamicLinksGenerator;
use App\Services\FirebaseDynamicLinks\Exceptions\NotSupportedMethodException;

class DynamicLink
{
    private ?string $pathForLink = null;

    public function __construct(
        private string $title,
        private ?string $description = '',
        private ?string $imageUrl = ''
    )
    {
    }

    public function setPath(string $path): self
    {
        $this->pathForLink = $path;

        return $this;
    }

    public function handle(): DynamicLinkResponse
    {
        $generationMethod = config('firebase-dynamic-links.generation_method');

        $builder = match ($generationMethod) {
            'google_firebase' => new FirebaseDynamicLinksGenerator(),
            'manual' => new ManualDynamicLinksGenerator(),
            default => throw new NotSupportedMethodException(
                'Method [' . $generationMethod . '] not support for Dynamic link.'
            ),
        };

        $builder->setMetaTags($this->title, $this->description, $this->imageUrl);

        $builder->setPathForLink($this->pathForLink);

        return $builder->generate();
    }
}
