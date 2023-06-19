<?php

namespace Modules\ApiV1\Actions\RecommendRole;

use App\Models\Application;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;

class RecommendRoleCheckRights
{
    public function handle(Application $application, ?int $roleId = null): void
    {
        if ($roleId) {
            Gate::allowIf(function ($user) use ($application, $roleId) {
                $projectId = optional(Role::toBase()->select('project_id')->whereId($roleId)->first())->project_id;

                return $user->getKey() === $application->role->project->user_id
                    && $user->projects()->whereId($projectId)->exists();
            });
        } else {
            Gate::allowIf(fn ($user) => $user->getKey() === $application->role->project->user_id);
        }
    }
}
