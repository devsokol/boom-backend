<?php

namespace Modules\MobileV1\Actions\ActorNotification;

use App\Models\Actor;

class ActorNotificationBadge
{
    public function handle(?Actor $actor): int
    {
        return $actor->actorNotifications()->whereIsRead(false)->count() ?? 0;
    }
}
