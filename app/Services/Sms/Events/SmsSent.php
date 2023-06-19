<?php

namespace App\Services\Sms\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SmsSent
{
    use SerializesModels;

    public Collection $sms;

    public array $HTTPResponseBody;

    public function __construct(Collection $sms, array $HTTPResponseBody)
    {
        $this->sms = $sms;
        $this->HTTPResponseBody = $HTTPResponseBody;
    }
}
