<?php

namespace Modules\MobileV1\Actions\ActorNotification;

use Illuminate\Pagination\LengthAwarePaginator;

class ActorNotificationList
{
    public function handle(): LengthAwarePaginator
    {
        return auth()
            ->user()
            ->actorNotifications()
            ->with('application.role.project.genre')
            ->orderBy('is_read')
            ->latest('id')
            ->jsonPaginate();
    }
}
