<?php

namespace Modules\ApiV1\Actions\ApplicationSelftape;

use App\Models\Application;
use Illuminate\Support\Facades\Gate;

class ApplicationSelftapeCheckRights
{
    public function handle(Application $application): void
    {
        Gate::allowIf(fn ($user) => $user->getKey() === $application->role->project->user_id);
    }
}
