<?php

namespace App\Services\Sms\Gateways;

use App\Services\Sms\Contracts\GatewayContract;
use App\Services\Sms\Contracts\IGatewayApiKeyAuthResolver;
use App\Services\Sms\Contracts\IGatewayBulkSendResolver;
use App\Services\Sms\Contracts\IGatewayResponseResolver;
use Psr\Http\Message\ResponseInterface as Response;

class InsightsGateway implements
    GatewayContract,
    IGatewayApiKeyAuthResolver,
    IGatewayResponseResolver,
    IGatewayBulkSendResolver
{
    private ?string $accessToken;
    private ?string $alphaName;

    /**
     * InsightsGateway constructor.
     *
     * @param  string  $accessToken
     * @param  string  $alphaName
     */
    public function __construct(?string $accessToken, ?string $alphaName)
    {
        $this->accessToken = $accessToken;
        $this->alphaName = $alphaName;
    }

    public function authenticationApiKeyResolve(): array
    {
        return [
            'AccessToken' => $this->accessToken,
        ];
    }

    public function baseUrl(): string
    {
        return 'https://data.insights.re/smsapi';
    }

    public function alpha(): string
    {
        return $this->alphaName;
    }

    public function sendMethod(): string
    {
        return 'POST';
    }

    public function bodyType(): string
    {
        return 'json';
    }

    public function prepareMessageForSingleSend(?string $number, string $from, string $message): array
    {
        return $this->prepareMessageParameters($number, $from, $message);
    }

    public function prepareMessageForBulkSend(array $numbers, string $from, string $message): array
    {
        return array_reduce($numbers, function ($carry, $number) use ($from, $message) {
            if (! empty($number)) {
                $carry[] = $this->prepareMessageParameters($number, $from, $message);
            }

            return $carry;
        }, []);
    }

    private function prepareMessageParameters(?string $number, string $from, string $message): array
    {
        return [
            'id' => uniqid(),
            'destaddr' => $number,
            'sourceaddr' => $from,
            'msg' => $message,
        ];
    }

    public function responseResolve(?Response $response): array
    {
        $params = [];
        if ($response) {
            $forbiddenCharacters = ['﻿'];
            $sanitizeBody = str_replace($forbiddenCharacters, '', $response->getBody()->getContents());
            $decodedResponse = json_decode($sanitizeBody);
            $params = is_array($decodedResponse) ? (array) current($decodedResponse) : [];
        }

        return $params;
    }
}
