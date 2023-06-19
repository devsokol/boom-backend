<?php

namespace Modules\MobileV1\Notifications;

use App\Broadcasting\UserDBNotificationChannel;
use App\Enums\UserNotificationStatus;

class OfferRoleDeclinedNotification extends AbstractActorNotification
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
        return __(':actor_name has rejected the role the new role offer :role_name.', [
            'actor_name' =>  $this->application->actor->getFullName(),
            'role_name'  => $this->application->role->name,
        ]);
    }

    public function status(): ?int
    {
        return UserNotificationStatus::OFFER_ROLE_DECLINED->value;
    }
}
