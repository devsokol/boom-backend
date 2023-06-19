<?php

namespace Modules\ApiV1\Actions\RoleMaterial;

use App\Models\RoleMaterial;

class RoleMaterialUpdate
{
    public function handle(RoleMaterial $roleMaterial, array $data): bool
    {
        $roleMaterial->unsetRelation('role');

        return $roleMaterial->update($data);
    }
}
