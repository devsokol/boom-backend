<?php

namespace Modules\ApiV1\Actions\ApplicationSelftapeMaterial;

use App\Models\ApplicationSelftapeMaterial;

class ApplicationSelftapeMaterialUpdate
{
    public function handle(ApplicationSelftapeMaterial $applicationSelftapeMaterial, array $data): bool
    {
        $applicationSelftapeMaterial->unsetRelation('applicationSelftape');

        return $applicationSelftapeMaterial->update($data);
    }
}
