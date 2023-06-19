<?php

namespace App\Services\Sms\Contracts;

interface IGatewayBulkSendResolver
{
    public function prepareMessageForBulkSend(array $numbers, string $from, string $message): array;
}
