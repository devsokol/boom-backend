<?php

namespace Modules\MobileV1\Actions\Application;

use App\Models\Role;
use App\Models\Application;
use App\Enums\ProjectStatus;
use Illuminate\Http\Response;
use App\Enums\ApplicationStatus;
use App\Helpers\DBPreventDuplicateHelper;

class ApplicationApplyForRole
{
    public function handle(Role $role): Application
    {
        if ($role->project->status !== ProjectStatus::ACTIVE) {
            abort(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                __('You cannot apply for the :role_name role because the :film film has not a public status', [
                    'role_name' => $role->name,
                    'film' => $role->project->name,
                ])
            );
        }

        $application = new Application(['status' => ApplicationStatus::IN_REVIEW->value]);

        $application->actor()->associate(auth()->user());

        return DBPreventDuplicateHelper::execute(function () use ($role, $application) {
            return $role->applications()->save($application);
        });
    }
}
