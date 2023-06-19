<?php

namespace Modules\MobileV1\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuditionDirectMessageMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        private string $actorName,
        private string $message
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(__(':actorName send message', ['actorName' => $this->actorName]))
            ->markdown('mobilev1::emails.audition_direct_message', [
                'actorName' => $this->actorName,
                'message' => $this->message,
            ]);
    }
}
