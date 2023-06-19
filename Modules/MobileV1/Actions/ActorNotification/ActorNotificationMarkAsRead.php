<?php

namespace Modules\MobileV1\Actions\ActorNotification;

use App\Models\ActorNotification;

class ActorNotificationMarkAsRead
{
    public function handle(ActorNotification $notification): bool
    {
        return $notification->update(['is_read' => true]);
    }
}
