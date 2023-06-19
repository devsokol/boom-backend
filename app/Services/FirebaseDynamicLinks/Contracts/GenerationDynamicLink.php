<?php

namespace App\Services\FirebaseDynamicLinks\Contracts;

use App\Services\FirebaseDynamicLinks\DynamicLinkResponse;

interface GenerationDynamicLink
{
    public function generate(): DynamicLinkResponse;
}
