<?php

namespace App\Services\Sms\Traits;

use App\Services\Sms\Contracts\GatewayContract;

trait HasGatewayApiKeyAuth
{
    /**
     * @var GatewayContract
     */
    private GatewayContract $driver;

    /**
     * @var string
     */
    private string $apiUrl;

    private function authByApiKey(array $queryVars): void
    {
        if (! empty($queryVars)) {
            $queryParams = http_build_query($queryVars);
            $separator = (parse_url($this->apiUrl, PHP_URL_QUERY) ? '&' : '?');
            $this->apiUrl = $this->apiUrl . $separator . $queryParams;
        }
    }
}
