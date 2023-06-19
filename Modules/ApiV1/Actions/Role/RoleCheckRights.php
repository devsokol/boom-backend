<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Project;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;

class RoleCheckRights
{
    public function handle(Project|Role $model): void
    {
        if ($model instanceof Project) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->user_id);
        } elseif ($model instanceof Role) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->project->user_id);
        }
    }
}
