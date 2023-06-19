<?php

namespace App\Services\Sms\Contracts;

interface GatewayContract
{
    /**
     * Set base url for each defined driver.
     */
    public function baseUrl(): string;

    /**
     * Alpha name from which the shipment is sent.
     */
    public function alpha(): string;

    /**
     * Method for sending HTTP request.
     *
     * @return string GET|POST
     */
    public function sendMethod(): string;

    /**
     * Example sending as: query (GET), json (PUT|POST...), form_params (POST), body (POST raw data).
     *
     * @return string
     */
    public function bodyType(): string;

    /**
     * Transmitted data format.
     *
     * @param  string  $number
     * @param  string  $from
     * @param  string  $message
     * @return array
     */
    public function prepareMessageForSingleSend(string $number, string $from, string $message): array;
}
