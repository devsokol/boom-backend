<?php

namespace Modules\MobileV1\Actions\Application;

use App\Models\Application;
use App\Models\Role;

class ApplicationIsAlreadyApplied
{
    public function handle(Role $role): bool
    {
        return Application::where([['actor_id', auth()->user()->getKey()], ['role_id', $role->id]])->exists();
    }
}
