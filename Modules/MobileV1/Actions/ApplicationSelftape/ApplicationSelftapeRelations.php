<?php

namespace Modules\MobileV1\Actions\ApplicationSelftape;

use App\Models\Application;

class ApplicationSelftapeRelations
{
    public function handle(Application $application): void
    {
        $application->applicationSelftape->load([
            'applicationSelftapeMaterials.materialType',
        ]);
    }
}
