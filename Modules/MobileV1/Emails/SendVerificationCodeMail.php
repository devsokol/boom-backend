<?php

namespace Modules\MobileV1\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerificationCodeMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        private string|int $code,
        private string $verificationCodeLifetime
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
            ->subject(__('Confirmation account registration in: :app_name', [
                'app_name' => config('app.name'),
            ]))
            ->markdown('mobilev1::emails.verification_code', [
                'code' => $this->code,
                'minutes' => $this->verificationCodeLifetime,
            ]);
    }
}
