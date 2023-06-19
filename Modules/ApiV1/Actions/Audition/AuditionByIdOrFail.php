<?php

namespace Modules\ApiV1\Actions\Audition;

use App\Models\Audition;

class AuditionByIdOrFail
{
    public function handle(int $auditionId): Audition
    {
        return Audition::with('auditionMaterials.materialType')->findOrFail($auditionId);
    }
}
