<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Role;

class RoleChangeAmountOfViewedApplications
{
    public function handle(Role $role, int $amount = 1, bool $isIncrement = true): void
    {
        $role->load('project.user');

        $userId = $role->project->user->id;

        $user = $role->userViewedApplications()->whereUserId($userId)->first();
        
        if ($user) {
            $amountOfViews = $user->pivot->amount_viewed_applications;

            $res = $isIncrement
                ? $amountOfViews + $amount
                : $amountOfViews - $amount;

            $role->userViewedApplications()->sync([
                $userId => [
                    'amount_viewed_applications' => max($res, 0),
                ],
            ]);
        }
    }
}
