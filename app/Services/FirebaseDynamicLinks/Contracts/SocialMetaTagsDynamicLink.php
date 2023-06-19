<?php

namespace App\Services\FirebaseDynamicLinks\Contracts;

interface SocialMetaTagsDynamicLink
{
    public function setMetaTags(string $title, ?string $description, ?string $imageUrl): void;
}
