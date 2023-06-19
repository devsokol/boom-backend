<?php

namespace App\Services\Sms\Contracts;

use Psr\Http\Message\ResponseInterface as Response;

interface IGatewayResponseResolver
{
    public function responseResolve(?Response $response): array;
}
