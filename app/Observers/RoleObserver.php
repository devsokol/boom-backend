<?php

namespace App\Observers;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleObserver
{
    public function deleting(Role $role): void
    {
        $role->load([
            'applications',
            'roleMaterials',
        ]);

        DB::transaction(function () use ($role) {
            $role->actorBookmarks()->sync([]);

            $role->personalSkills()->sync([]);

            $role->roleMaterials->each->delete();

            $role->applications->each->delete();

            $role->pickShootingDates()->delete();

            $role->userViewedApplications()->sync([]);
        });
    }
}
