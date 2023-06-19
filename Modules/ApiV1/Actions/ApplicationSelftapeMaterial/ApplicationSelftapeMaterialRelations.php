<?php

namespace Modules\ApiV1\Actions\ApplicationSelftapeMaterial;

use App\Models\ApplicationSelftapeMaterial;

class ApplicationSelftapeMaterialRelations
{
    public function handle(ApplicationSelftapeMaterial $applicationSelftapeMaterial): void
    {
        $applicationSelftapeMaterial->load('materialType');
    }
}
