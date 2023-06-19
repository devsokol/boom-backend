<?php

namespace Modules\ApiV1\Notifications;

use App\Broadcasting\ActorDBNotificationChannel;
use App\Enums\ActorNotificationStatus;
use NotificationChannels\Fcm\FcmChannel;

class RoleOfferNotification extends AbstractActorNotification
{
    public function via($notifiable): array
    {
        $channels = [ActorDBNotificationChannel::class];

        if ($this->application->actor->isNotificationEnabled('role_offer_notification')) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    public function title(): ?string
    {
        return __('New Role!');
    }

    public function body(): ?string
    {
        return __('You applied for :old_role_name, and we think that :new_role_name '
        . 'will be a good match for you. Are you interested in this?', [
            'old_role_name' => $this->application->role->name,
            'new_role_name' => $this->newRoleName(),
        ]);
    }

    public function status(): ?int
    {
        return ActorNotificationStatus::ROLE_OFFER->value;
    }

    private function newRoleName(): ?string
    {
        $this->application->load('recommendRole');

        return $this->application?->recommendRole?->role?->name;
    }
}
