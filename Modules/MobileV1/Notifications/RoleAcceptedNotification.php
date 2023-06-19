<?php

namespace Modules\MobileV1\Notifications;

use App\Broadcasting\UserDBNotificationChannel;
use App\Enums\UserNotificationStatus;

class RoleAcceptedNotification extends AbstractActorNotification
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
        return __(':actor_name has been cast for the :role_name role.', [
            'actor_name' => $this->application->actor->getFullName(),
            'role_name'  => $this->application->role->name,
        ]);
    }

    public function status(): ?int
    {
        return UserNotificationStatus::ROLE_ACCEPTED->value;
    }
}
