<?php

namespace App\Observers;

use App\Models\Audition;

class AuditionObserver
{
    public function deleting(Audition $audition): void
    {
        $audition->load('auditionMaterials');

        $audition->auditionMaterials->each->delete();
    }
}
