<?php

namespace Modules\MobileV1\Actions\Audition;

use App\Models\Application;
use Illuminate\Support\Facades\Gate;

class AuditionRightCheck
{
    public function handle(Application $application): void
    {
        Gate::allowIf(fn ($user) => $user->getKey() === $application->actor_id);
    }
}
