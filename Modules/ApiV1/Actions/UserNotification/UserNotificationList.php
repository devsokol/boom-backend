<?php

namespace Modules\ApiV1\Actions\UserNotification;

use Illuminate\Database\Eloquent\Collection;

class UserNotificationList
{
    public function handle(): Collection
    {
        return auth()
            ->user()
            ->userNotifications()
            ->with('application.role.project.genre')
            ->orderBy('is_read')
            ->latest('id')
            ->get();
    }
}
