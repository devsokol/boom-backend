<?php

namespace Modules\ApiV1\Actions\Application;

use App\Models\Application;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;

class ApplicationListCheckRights
{
    public function handle(Role|Application $model): void
    {
        if ($model instanceof Role) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->project->user_id);
        } elseif ($model instanceof Application) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->role->project->user_id);
        }
    }
}
