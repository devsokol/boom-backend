<?php

namespace Modules\MobileV1\Actions\Role;

use App\Models\Role;

class RoleLoadOnlyActorApplications
{
    public function handle(Role $role): void
    {
        $actorId = auth()->user()->getKey();

        $role->load([
            'applications' => function ($q) use ($actorId) {
                $q->whereActorId($actorId);
            },
            'applications.audition',
        ]);
    }
}
