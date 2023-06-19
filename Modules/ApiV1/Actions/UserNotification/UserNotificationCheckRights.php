<?php

namespace Modules\ApiV1\Actions\UserNotification;

use App\Models\UserNotification;
use Illuminate\Support\Facades\Gate;

class UserNotificationCheckRights
{
    public function handle(UserNotification $notification): void
    {
        Gate::allowIf(fn ($user) => $user->getKey() === $notification->application->role->project->user_id);
    }
}
