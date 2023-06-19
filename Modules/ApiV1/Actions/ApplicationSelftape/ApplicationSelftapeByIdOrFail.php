<?php

namespace Modules\ApiV1\Actions\ApplicationSelftape;

use App\Models\ApplicationSelftape;

class ApplicationSelftapeByIdOrFail
{
    public function handle(int $applicationSelftapeId): ApplicationSelftape
    {
        return ApplicationSelftape::query()
            ->with('applicationSelftapeMaterials.materialType')
            ->findOrFail((int) $applicationSelftapeId);
    }
}
