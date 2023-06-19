<?php

namespace Modules\ApiV1\Actions\RecommendRole;

use App\Enums\ApplicationStatus;
use App\Enums\ProjectStatus;
use App\Enums\RecommendRoleStatus;
use App\Models\Application;
use App\Models\RecommendRole;
use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RecommendRoleStore
{
    public function handle(int $roleId, Application $application): RecommendRole
    {
        $role = Role::findOrFail($roleId);

        if ($role->project->status !== ProjectStatus::ACTIVE) {
            abort(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                __('You cannot apply for the :role_name role because the :film film has not a public status', [
                    'role_name' => $role->name,
                    'film' => $role->project->name,
                ])
            );
        }

        if ($application->status === ApplicationStatus::ROLE_OFFER) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('You cannot submit a request twice'));
        }

        if ($application->recommendRole) {
            $application->recommendRole->delete();
        }

        return DB::transaction(function () use ($roleId, $application) {
            $recommendRole = $application->recommendRole()->create([
                'role_id' => $roleId,
                'status' => RecommendRoleStatus::IN_REVIEW->value,
            ]);

            $application->update(['status' => ApplicationStatus::ROLE_OFFER]);

            return $recommendRole;
        });
    }
}
