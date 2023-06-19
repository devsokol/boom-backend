<?php

namespace Modules\MobileV1\Actions\RecommendRole;

use App\Enums\RecommendRoleStatus;
use App\Models\Application;
use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\MobileV1\Actions\Application\ApplicationApplyForRole;
use Modules\MobileV1\Notifications\OfferRoleAcceptedNotification;

class RecommendRoleApply
{
    public function handle(Application $application): void
    {
        if ($application->recommendRole->status !== RecommendRoleStatus::IN_REVIEW) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('It is not possible to change the status twice'));
        }

        $newApplication = DB::transaction(function () use ($application) {
            $recommendedRoleId = $application->recommendRole->role_id;

            $application->delete();

            $role = Role::find($recommendedRoleId);

            return (new ApplicationApplyForRole())->handle($role);
        });

        $application->role->project->user->notify(new OfferRoleAcceptedNotification($newApplication));
    }
}
