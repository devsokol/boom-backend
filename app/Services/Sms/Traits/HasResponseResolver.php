<?php

namespace App\Services\Sms\Traits;

use App\Services\Sms\Contracts\GatewayContract;
use App\Services\Sms\Contracts\IGatewayResponseResolver;
use Psr\Http\Message\ResponseInterface as Response;

trait HasResponseResolver
{
    /**
     * @var GatewayContract
     */
    private GatewayContract $driver;

    private function responseResolve(Response $response): array|string
    {
        if ($this->driver instanceof IGatewayResponseResolver) {
            return $this->driver->responseResolve($response);
        } else {
            return $response->getBody()->getContents();
        }
    }
}
