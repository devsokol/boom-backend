<?php

namespace Modules\MobileV1\Actions\RecommendRole;

use App\Enums\RecommendRoleStatus;
use App\Models\Application;
use Illuminate\Http\Response;
use Modules\MobileV1\Notifications\OfferRoleDeclinedNotification;

class RecommendRoleReject
{
    public function handle(Application $application): void
    {
        if ($application->recommendRole->status !== RecommendRoleStatus::IN_REVIEW) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('It is not possible to change the status twice'));
        }

        $application->recommendRole->update(['status' => RecommendRoleStatus::REJECTED->value]);

        $application->role->project->user->notify(new OfferRoleDeclinedNotification($application));
    }
}
