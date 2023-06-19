<?php

namespace Modules\ApiV1\Actions\AuditionMaterial;

use App\Models\AuditionMaterial;

class AuditionMaterialRelations
{
    public function handle(AuditionMaterial $auditionMaterial): void
    {
        $auditionMaterial->load('materialType');
    }
}
