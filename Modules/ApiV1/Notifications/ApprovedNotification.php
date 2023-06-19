<?php

namespace Modules\ApiV1\Notifications;

use App\Broadcasting\ActorDBNotificationChannel;
use App\Enums\ActorNotificationStatus;
use NotificationChannels\Fcm\FcmChannel;

class ApprovedNotification extends AbstractActorNotification
{
    public function via($notifiable): array
    {
        $channels = [ActorDBNotificationChannel::class];

        if ($this->application->actor->isNotificationEnabled('role_approve_notification')) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    public function title(): ?string
    {
        return __('You\'ve been chosen as :character_name in :film_name.', [
            'character_name' => $this->application->role->name,
            'film_name' => $this->application->role->project->name,
        ]);
    }

    public function body(): ?string
    {
        return __('Congratulations! You have been selected for the role of :character_name in :production_name.', [
            'character_name' => $this->application->role->name,
            'production_name' => $this->application->role->project->user->company_name,
        ]);
    }

    public function status(): ?int
    {
        return ActorNotificationStatus::ROLE_OFFERED->value;
    }
}
