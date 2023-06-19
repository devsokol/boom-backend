<?php

namespace Modules\MobileV1\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Exceptions\InvalidValueException;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\MobileV1\Emails\UserNotificationMail;

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

    public function toArray(mixed $notifiable)
    {
        return $this->templateResolver();
    }

    public function toMail(mixed $notifiable): Mailable
    {
        return (new UserNotificationMail($this->title(), $this->body()))
            ->to($this->application->role->project->user->email);
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
