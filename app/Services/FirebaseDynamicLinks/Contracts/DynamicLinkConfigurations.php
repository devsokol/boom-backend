<?php

namespace App\Services\FirebaseDynamicLinks\Contracts;

use App\Services\FirebaseDynamicLinks\DynamicLinkConfig;

interface DynamicLinkConfigurations
{
    public function configurations(): DynamicLinkConfig;
}
