<?php

namespace App\Services\FirebaseDynamicLinks\Contracts;

interface ExtendPathDynamicLink
{
    public function setPathForLink(string $path): void;
}
