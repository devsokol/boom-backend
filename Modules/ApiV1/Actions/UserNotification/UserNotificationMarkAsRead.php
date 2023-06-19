<?php

namespace Modules\ApiV1\Actions\UserNotification;

use App\Models\UserNotification;

class UserNotificationMarkAsRead
{
    public function handle(UserNotification $notification): bool
    {
        return $notification->update(['is_read' => true]);
    }
}
