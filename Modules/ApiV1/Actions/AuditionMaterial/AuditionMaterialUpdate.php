<?php

namespace Modules\ApiV1\Actions\AuditionMaterial;

use App\Models\AuditionMaterial;

class AuditionMaterialUpdate
{
    public function handle(AuditionMaterial $auditionMaterial, array $data): bool
    {
        $auditionMaterial->unsetRelation('audition');

        return $auditionMaterial->update($data);
    }
}
