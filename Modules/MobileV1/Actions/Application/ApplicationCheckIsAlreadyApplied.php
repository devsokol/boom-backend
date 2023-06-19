<?php

namespace Modules\MobileV1\Actions\Application;

use App\Models\Role;
use Illuminate\Http\Response;

class ApplicationCheckIsAlreadyApplied
{
    public function handle(Role $role): void
    {
        $applicationIsAlreadyApplied = (new ApplicationIsAlreadyApplied())->handle($role);

        abort_if(
            $applicationIsAlreadyApplied,
            Response::HTTP_CONFLICT,
            __('You have already applied for the role')
        );
    }
}
