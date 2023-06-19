<?php

namespace Modules\ApiV1\Actions\AuditionMaterial;

use App\Models\Audition;
use App\Models\AuditionMaterial;
use Illuminate\Support\Facades\Gate;

class AuditionMaterialCheckRights
{
    public function handle(Audition|AuditionMaterial $model): void
    {
        if ($model instanceof Audition) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->application->role->project->user_id);
        } elseif ($model instanceof AuditionMaterial) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->audition->application->role->project->user_id);
        }
    }
}
