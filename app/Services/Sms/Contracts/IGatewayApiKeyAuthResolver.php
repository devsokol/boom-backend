<?php

namespace App\Services\Sms\Contracts;

interface IGatewayApiKeyAuthResolver
{
    public function authenticationApiKeyResolve(): array;
}
