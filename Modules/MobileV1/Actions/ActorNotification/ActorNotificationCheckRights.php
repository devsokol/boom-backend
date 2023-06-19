<?php

namespace Modules\MobileV1\Actions\ActorNotification;

use App\Models\ActorNotification;
use Illuminate\Support\Facades\Gate;

class ActorNotificationCheckRights
{
    public function handle(ActorNotification $notification): void
    {
        Gate::allowIf(fn ($user) => $user->getKey() === $notification->application->actor_id);
    }
}
