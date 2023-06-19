<?php

namespace Modules\ApiV1\Notifications;

use App\Broadcasting\ActorDBNotificationChannel;
use App\Enums\ActorNotificationStatus;
use NotificationChannels\Fcm\FcmChannel;

class RejectedNotification extends AbstractActorNotification
{
    public function via($notifiable): array
    {
        $channels = [ActorDBNotificationChannel::class];

        if ($this->application->actor->isNotificationEnabled('role_reject_notification')) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    public function title(): ?string
    {
        return __('You did not get the role of :character_name.', [
            'character_name' => $this->application->role->name,
        ]);
    }

    public function body(): ?string
    {
        return __('Unfortunately, :production_name did not choose you for the role of :character_name this time.', [
            'character_name' => $this->application->role->name,
            'production_name' => $this->application->role->project->user->company_name,
        ]);
    }

    public function status(): ?int
    {
        return ActorNotificationStatus::REJECTED->value;
    }
}
