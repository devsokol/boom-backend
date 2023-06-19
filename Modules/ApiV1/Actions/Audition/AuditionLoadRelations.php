<?php

namespace Modules\ApiV1\Actions\Audition;

use App\Models\Audition;

class AuditionLoadRelations
{
    public function handle(?Audition $audition): void
    {
        if ($audition && $audition->exists) {
            $audition->load('auditionMaterials.materialType');
        }
    }
}
