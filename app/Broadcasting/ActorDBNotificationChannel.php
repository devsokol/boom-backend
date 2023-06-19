<?php

namespace App\Broadcasting;

use App\Models\ActorNotification;
use Illuminate\Notifications\Notification;

class ActorDBNotificationChannel extends Notification
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        $template = $notification->toArray($notifiable);

        ActorNotification::create([
            ...$template,
            'actor_id' => $notifiable->id,
            'application_id' => $notification->application->getKey(),
        ]);
    }
}
