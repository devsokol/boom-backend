<?php

namespace App\Services\Sms\Jobs;

use App\Services\Sms\Facades\Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private string $body,
        private array|string $number,
    ) {
    }

    public function handle(): void
    {
        Sms::send($this->body, $this->number);
    }
}
