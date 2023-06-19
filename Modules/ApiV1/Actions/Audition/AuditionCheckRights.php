<?php

namespace Modules\ApiV1\Actions\Audition;

use App\Models\Application;
use Illuminate\Support\Facades\Gate;

class AuditionCheckRights
{
    public function handle(Application $application): void
    {
        Gate::allowIf(fn ($user) => $user->getKey() === $application->role->project->user_id);
    }
}
