<?php

namespace Modules\MobileV1\Notifications;

use App\Broadcasting\UserDBNotificationChannel;
use App\Enums\UserNotificationStatus;

class AuditionAcceptedNotification extends AbstractActorNotification
{
    public function via($notifiable): array
    {
        return [UserDBNotificationChannel::class, 'mail'];
    }

    public function title(): ?string
    {
        return ' ';
    }

    public function body(): ?string
    {
        return __(':actor_name has accepted audition invitation.', [
            'actor_name' =>  $this->application->actor->getFullName(),
        ]);
    }

    public function status(): ?int
    {
        return UserNotificationStatus::AUDITION_ACCEPTED->value;
    }
}
