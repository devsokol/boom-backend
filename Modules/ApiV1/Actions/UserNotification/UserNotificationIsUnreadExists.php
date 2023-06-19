<?php

namespace Modules\ApiV1\Actions\UserNotification;

class UserNotificationIsUnreadExists
{
    public function handle(): bool
    {
        return auth()->user()->userNotifications()->whereIsRead(false)->exists();
    }
}
