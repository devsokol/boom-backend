<?php

namespace Modules\ApiV1\Actions\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Gate;

class ProjectCheckRights
{
    public function handle(Project $project): void
    {
        Gate::allowIf(fn ($user) => $user->getKey() === $project->user_id);
    }
}
