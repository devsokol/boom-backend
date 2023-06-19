<?php

namespace Modules\MobileV1\Actions\Role;

use App\Models\Role;

class RoleRemoveBookmark
{
    public function handle(Role $role): int
    {
        $actorId = auth()->user()->getKey();

        return $role->actorBookmarks()->detach($actorId);
    }
}
