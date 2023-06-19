<?php

namespace Modules\MobileV1\Actions\Application;

use App\Models\Application;
use Illuminate\Support\Facades\Gate;

class ApplicationCheckRights
{
    public function handle(Application $application): void
    {
        Gate::allowIf(fn ($user) => $user->getKey() === $application->actor_id);
    }
}
