<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Role;

class RoleByIdOrFail
{
    public function handle(int $roleId): Role
    {
        return Role::query()
            ->with([
                'ethnicity',
                'personalSkills',
                'roleMaterials',
                'roleMaterials.materialType',
                'pickShootingDates',
                'currency',
                'paymentType',
                'country',
                'project.user',
                'project.genre',
                'project.projectType',
            ])
            ->findOrFail($roleId);
    }
}
