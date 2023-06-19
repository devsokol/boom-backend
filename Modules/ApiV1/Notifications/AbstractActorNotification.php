<?php

namespace Modules\ApiV1\Notifications;

use App\Broadcasting\ActorDBNotificationChannel;
use App\Exceptions\InvalidValueException;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\MobileV1\Actions\ActorNotification\ActorNotificationBadge;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\ApnsConfig;

abstract class AbstractActorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application)
    {
        $this->checkRequirements();
    }

    public function title(): ?string
    {
        return null;
    }

    public function body(): ?string
    {
        return null;
    }

    public function status(): ?int
    {
        return null;
    }

    public function withDelay($notifiable)
    {
        return [
            ActorDBNotificationChannel::class => now(),
            FcmChannel::class => now()->addSeconds(5),
        ];
    }

    public function toFcm(mixed $notifiable): FcmMessage
    {
        //if the notification is not already created in the database - add it plus one
        $fixCounter = $this->application->wasChanged() ? 1 : 0;

        return FcmMessage::create()
            ->setNotification(
                \NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($this->title())
                ->setBody($this->body())
                ->setImage(asset('/assets/icons/logo.svg'))
            )
            ->setApns(
                ApnsConfig::create()
                    ->setPayload([
                        'aps' => [
                            'badge' => (new ActorNotificationBadge())->handle($notifiable) + $fixCounter,
                        ],
                    ])
            );
    }

    public function toArray(mixed $notifiable)
    {
        return $this->templateResolver();
    }

    protected function checkRequirements(): void
    {
        if (! $this->title() || ! $this->body() || is_null($this->status())) {
            throw new InvalidValueException(
                'One of the methods: [titleTemplate(), bodyTemplate(), status()] returns an invalid value'
            );
        }
    }

    protected function templateResolver(): array
    {
        return [
            'title' => $this->title(),
            'body' => $this->body(),
            'status' => $this->status(),
        ];
    }
}
