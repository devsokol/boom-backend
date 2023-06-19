<?php

namespace Modules\ApiV1\Notifications;

use App\Broadcasting\ActorDBNotificationChannel;
use App\Enums\ActorNotificationStatus;
use NotificationChannels\Fcm\FcmChannel;

class AuditionNotification extends AbstractActorNotification
{
    public function via(mixed $notifiable): array
    {
        $channels = [ActorDBNotificationChannel::class];

        if ($this->application->actor->isNotificationEnabled('audition_notification')) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    public function title(): ?string
    {
        return __('Attention!');
    }

    public function body(): ?string
    {
        return __('You have been selected for an audition for the :role_name '
        . 'role in :film_name. Please accept the date and time. ', [
            'role_name' => $this->application->role->name,
            'film_name' => $this->application->role->project->name,
        ]);
    }

    public function status(): ?int
    {
        return ActorNotificationStatus::AUDITION->value;
    }
}
