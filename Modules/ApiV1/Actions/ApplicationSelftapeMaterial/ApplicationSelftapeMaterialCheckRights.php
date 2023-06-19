<?php

namespace Modules\ApiV1\Actions\ApplicationSelftapeMaterial;

use App\Models\ApplicationSelftape;
use App\Models\ApplicationSelftapeMaterial;
use Illuminate\Support\Facades\Gate;

class ApplicationSelftapeMaterialCheckRights
{
    public function handle(ApplicationSelftape|ApplicationSelftapeMaterial $model): void
    {
        if ($model instanceof ApplicationSelftapeMaterial) {
            Gate::allowIf(
                fn ($user) => $user->getKey() === $model
                    ->applicationSelftape
                    ->application
                    ->role
                    ->project
                    ->user_id
            );
        } elseif ($model instanceof ApplicationSelftape) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->application->role->project->user_id);
        }
    }
}
