<?php

namespace Modules\ApiV1\Actions\RoleMaterial;

use App\Models\RoleMaterial;

class RoleMaterialRelations
{
    public function handle(RoleMaterial $roleMaterial): void
    {
        $roleMaterial->load([
            'materialType',
        ]);
    }
}
