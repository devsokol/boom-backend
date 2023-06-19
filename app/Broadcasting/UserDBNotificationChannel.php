<?php

namespace App\Broadcasting;

use App\Models\UserNotification;
use Illuminate\Notifications\Notification;

class UserDBNotificationChannel extends Notification
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        $template = $notification->toArray($notifiable);

        UserNotification::create([
            ...$template,
            'user_id' => $notifiable->id,
            'application_id' => $notification->application->getKey(),
        ]);
    }
}
