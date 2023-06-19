<?php

namespace Modules\ApiV1\Notifications;

use App\Broadcasting\ActorDBNotificationChannel;
use App\Enums\ActorNotificationStatus;
use Carbon\Carbon;
use NotificationChannels\Fcm\FcmChannel;

class SelfTapeRequestNotification extends AbstractActorNotification
{
    public function via(mixed $notifiable): array
    {
        $channels = [ActorDBNotificationChannel::class];

        if ($this->application->actor->isNotificationEnabled('selftape_notification')) {
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
        return __('You have been asked for a self-tape for the :role_name '
        . 'role in :film_name. Please provide more details by :deadline ', [
            'role_name' => $this->application->role->name,
            'film_name' => $this->application->role->project->name,
            'deadline' => $this->getDeadlineDatetime(),
        ]);
    }

    public function status(): ?int
    {
        return ActorNotificationStatus::SELFTAPE_REQUEST->value;
    }

    private function getDeadlineDatetime(): ?Carbon
    {
        $this->application->load('applicationSelftape');

        return $this->application->applicationSelftape?->deadline_datetime;
    }
}
