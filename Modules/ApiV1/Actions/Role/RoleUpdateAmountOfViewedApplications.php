<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Role;

class RoleUpdateAmountOfViewedApplications
{
    public function handle(Role $role): void
    {
        $role->loadCount(['applications' => function ($q) {
            $q->whereNotNull('actor_id');
        }]);

        if ($role->applications_count > 0) {
            $userId = auth()->user()->getKey();

            $role->userViewedApplications()->sync([
                $userId => [
                    'amount_viewed_applications' => $role->applications_count,
                ],
            ]);
        }
    }
}
