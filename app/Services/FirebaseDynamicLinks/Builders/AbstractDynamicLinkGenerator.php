<?php

namespace App\Services\FirebaseDynamicLinks\Builders;

use GuzzleHttp\Psr7\Uri;
use App\Services\FirebaseDynamicLinks\DynamicLinkConfig;
use App\Services\FirebaseDynamicLinks\DynamicLinkResponse;
use App\Services\FirebaseDynamicLinks\Contracts\ExtendPathDynamicLink;
use App\Services\FirebaseDynamicLinks\Contracts\GenerationDynamicLink;
use App\Services\FirebaseDynamicLinks\Contracts\DynamicLinkConfigurations;
use App\Services\FirebaseDynamicLinks\Contracts\SocialMetaTagsDynamicLink;

abstract class AbstractDynamicLinkGenerator implements
    DynamicLinkConfigurations,
    ExtendPathDynamicLink,
    GenerationDynamicLink,
    SocialMetaTagsDynamicLink
{
    protected string $metaTitle;

    protected string $metaDescription;

    protected string $metaImageUrl;

    protected ?string $pathForLink;

    abstract public function generate(): DynamicLinkResponse;

    public function setMetaTags(string $title, ?string $description, ?string $imageUrl): void
    {
        $this->metaTitle = $title;
        $this->metaDescription = $description;
        $this->metaImageUrl = $imageUrl;
    }

    public function setPathForLink(?string $path): void
    {
        $this->pathForLink = $path;
    }

    public function configurations(): DynamicLinkConfig
    {
        return new DynamicLinkConfig();
    }

    protected function getUrl(): string
    {
        $url = $this->configurations()->getLink();

        if ($this instanceof ExtendPathDynamicLink && ! empty($this->pathForLink)) {
            $uri = new Uri($url);

            $url = $uri->withPath($this->pathForLink);
        }

        return $url;
    }
}
