<?php

namespace Modules\ApiV1\Actions\AuditionMaterial;

use App\Models\Audition;
use App\Models\AuditionMaterial;

class AuditionMaterialStore
{
    public function handle(Audition $audition, array $data): AuditionMaterial
    {
        return $audition->auditionMaterials()->create($data);
    }
}
