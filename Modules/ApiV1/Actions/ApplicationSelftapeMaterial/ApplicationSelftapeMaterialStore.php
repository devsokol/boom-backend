<?php

namespace Modules\ApiV1\Actions\ApplicationSelftapeMaterial;

use App\Models\ApplicationSelftape;
use App\Models\ApplicationSelftapeMaterial;

class ApplicationSelftapeMaterialStore
{
    public function handle(ApplicationSelftape $applicationSelftape, array $data): ApplicationSelftapeMaterial
    {
        return $applicationSelftape->applicationSelftapeMaterials()->create($data);
    }
}
