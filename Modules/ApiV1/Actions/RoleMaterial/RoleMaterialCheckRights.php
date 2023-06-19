<?php

namespace Modules\ApiV1\Actions\RoleMaterial;

use App\Models\Role;
use App\Models\RoleMaterial;
use Illuminate\Support\Facades\Gate;

class RoleMaterialCheckRights
{
    public function handle(Role|RoleMaterial $model): void
    {
        if ($model instanceof Role) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->project->user_id);
        } elseif ($model instanceof RoleMaterial) {
            Gate::allowIf(fn ($user) => $user->getKey() === $model->role->project->user_id);
        }
    }
}
