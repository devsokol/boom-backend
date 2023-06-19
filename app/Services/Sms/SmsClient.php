<?php

namespace App\Services\Sms;

use App\Services\Sms\Contracts\GatewayContract;
use App\Services\Sms\Contracts\IGatewayApiKeyAuthResolver;
use App\Services\Sms\Contracts\IGatewayBulkSendResolver;
use App\Services\Sms\Events\SmsFailed;
use App\Services\Sms\Events\SmsSent;
use App\Services\Sms\Exceptions\UnsupportedParameterException;
use App\Services\Sms\Traits\HasGatewayApiKeyAuth;
use App\Services\Sms\Traits\HasResponseResolver;
use GuzzleHttp;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface as Response;

final class SmsClient
{
    use HasGatewayApiKeyAuth;
    use HasResponseResolver;

    /**
     * Guzzle Http Client Object.
     *
     * @var GuzzleHttp\Client
     */
    private Client $client;

    /**
     * Store response after make HTTP request.
     *
     * @var Response
     */
    private Response $response;

    /**
     * @var Collection
     */
    private Collection $massResponse;

    /**
     * Success status code.
     *
     * @var array
     */
    private array $successCodes = [200, 201];

    /**
     * @var GatewayContract
     */
    private GatewayContract $driver;

    /**
     * @var Collection
     */
    private Collection $data;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $apiUrl;

    /**
     * @var array|string
     */
    private $number;

    /**
     * @var string
     */
    private string $from;

    /**
     * @var string
     */
    private string $message;

    /**
     * @var int
     */
    private int $smsMailingChunkSize;

    public function __construct(GatewayContract $driverContract)
    {
        $this->massResponse = collect([]);

        $this->driver = $driverContract;
        $this->initHTTPClient();
        $this->smsMailingChunkSize = config('sms.sms_mailing_chunk_size', 50);
    }

    private function initHTTPClient(): void
    {
        $this->client = new GuzzleHttp\Client();
    }

    public function send(string $message, mixed $number): void
    {
        $this->number = $number;
        $this->message = $message;
        $this->from = $this->driver->alpha();
        $this->type = $this->driver->bodyType();
        $this->apiUrl = $this->driver->baseUrl();

        if ($this->driver instanceof IGatewayApiKeyAuthResolver) {
            $queryVars = $this->driver->authenticationApiKeyResolve();

            $this->authByApiKey($queryVars);
        }

        try {
            $this->executeRequest();

            $HTTPResponseBody = $this->responseHandler();

            if (method_exists($this->driver, 'success')) {
                call_user_func([$this->driver, 'success'], $HTTPResponseBody);
            }

            event(new SmsSent($this->data, $HTTPResponseBody));
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $HTTPResponseBody = $e->getResponse()->getBody()->getContents();

            if (method_exists($this->driver, 'error')) {
                call_user_func([$this->driver, 'error'], $e, $HTTPResponseBody);
            }

            event(new SmsFailed($this->data, $e, $HTTPResponseBody));
        }
    }

    private function executeRequest(): void
    {
        if ($this->isBulkSending()) {
            $this->bulkSend();
        } else {
            $this->singleSend();
        }
    }

    private function isBulkSending(): bool
    {
        return is_array($this->number) && $this->driver instanceof IGatewayBulkSendResolver;
    }

    private function bulkSend(): void
    {
        $preparedMessageList = $this->driver->prepareMessageForBulkSend($this->number, $this->from, $this->message);

        $this->data = collect($preparedMessageList);

        $this->data->chunk($this->smsMailingChunkSize)->each(function ($item) {
            $this->massResponse->add($this->client->request(
                $this->driver->sendMethod(),
                $this->apiUrl,
                $this->formData($item->toArray())
            ));
        });
    }

    private function singleSend(): void
    {
        if (is_array($this->number)) {
            $gatewayName = (new \ReflectionClass($this->driver))->getShortName();

            throw new UnsupportedParameterException("SMS Gateway [{$gatewayName}] is not implemented bulk send.");
        }

        $this->data = collect($this->driver->prepareMessageForSingleSend(
            $this->number,
            $this->from,
            $this->message
        ));

        $this->response = $this->client->request(
            $this->driver->sendMethod(),
            $this->apiUrl,
            $this->formData($this->data->toArray())
        );
    }

    private function responseHandler(): array
    {
        if (is_array($this->number)) {
            return array_reduce($this->massResponse->toArray(), function ($carry, $response) {
                $carry[] = $this->responseResolve($response);

                return $carry;
            }, []);
        }

        return $this->responseResolve($this->response);
    }

    /**
     * Check whether the HTTP request is success or failed.
     *
     * @return bool
     */
    private function isRequestSuccess(): bool
    {
        if (! in_array($this->response->getStatusCode(), $this->successCodes)) {
            return false;
        }

        return true;
    }

    private function formData(array $data): array
    {
        $supportedTypes = [
            'query',
            'body',
            'json',
            'form_params',
        ];

        if (! in_array($this->type, $supportedTypes)) {
            throw new UnsupportedParameterException("Parameter [{$this->type}] is not supported.");
        }

        return [$this->type => $data];
    }
}
