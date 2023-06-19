<?php

namespace App\Services\Sms\Events;

use Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SmsFailed
{
    use SerializesModels;

    public Collection $sms;

    public Exception $exception;

    public string $HTTPResponseBody;

    public function __construct(Collection $sms, Exception $e, string $HTTPResponseBody)
    {
        $this->sms = $sms;
        $this->HTTPResponseBody = $HTTPResponseBody;
        $this->exception = $e;
    }
}
