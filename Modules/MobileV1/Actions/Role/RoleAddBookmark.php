<?php

namespace Modules\MobileV1\Actions\Role;

use App\Models\Role;

class RoleAddBookmark
{
    public function handle(Role $role): array
    {
        $actorId = auth()->user()->getKey();

        return $role->actorBookmarks()->sync($actorId, false);
    }
}
