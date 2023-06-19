<?php

namespace Modules\ApiV1\Actions\RoleMaterial;

use App\Models\Role;
use App\Models\RoleMaterial;

class RoleMaterialCreate
{
    public function handle(Role $role, array $data): RoleMaterial
    {
        return $role->roleMaterials()->create($data);
    }
}
