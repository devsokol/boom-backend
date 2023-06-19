<?php

namespace Modules\MobileV1\Notifications;

use Illuminate\Mail\Mailable;
use App\Enums\UserNotificationStatus;
use App\Broadcasting\UserDBNotificationChannel;
use Modules\MobileV1\Emails\UserNotificationMail;

class RoleDeclinedNotification extends AbstractActorNotification
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
        return __(':actor_name has rejected the role :role_name.', [
            'actor_name' => $this->application->actor->getFullName(),
            'role_name'  => $this->application->role->name,
        ]);
    }

    public function status(): ?int
    {
        return UserNotificationStatus::ROLE_DECLINED->value;
    }

    public function toMail(mixed $notifiable): Mailable
    {
        $body = $this->body();

        $body .= "\n" . __('Reason of Rejection: :reason', [
            'reason' => $this->application->reject_reason,
        ]);

        return (new UserNotificationMail($this->title(), $body))
            ->to($this->application->role->project->user->email);
    }
}
