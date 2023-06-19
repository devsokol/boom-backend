<?php

namespace Modules\MobileV1\Notifications;

use App\Enums\UserNotificationStatus;
use Illuminate\Mail\Mailable;
use App\Broadcasting\UserDBNotificationChannel;
use Modules\MobileV1\Emails\UserNotificationMail;

class AuditionDeclinedNotification extends AbstractActorNotification
{
    public function via($notifiable): array
    {
        return [UserDBNotificationChannel::class, 'mail'];
    }

    public function title(): ?string
    {
        return __('Attention!');
    }

    public function body(): ?string
    {
        return __(':actor_name has rejected audition invitation.', [
            'actor_name' =>  $this->application->actor->getFullName(),
        ]);
    }

    public function status(): ?int
    {
        return UserNotificationStatus::AUDITION_DECLINED->value;
    }

    public function toMail(mixed $notifiable): Mailable
    {
        $body = $this->body();

        if (isset($this->application->audition->reject_reason)) {
            $body .= "\n" . __('Reason of Rejection: :reason', [
                'reason' => $this->application->audition->reject_reason,
            ]);
        }

        return (new UserNotificationMail($this->title(), $body))
            ->to($this->application->role->project->user->email);
    }
}
