<?php

namespace Modules\MobileV1\Actions\Audition;

use App\Enums\AuditionStatus;
use App\Models\Application;
use Illuminate\Http\Response;
use Modules\MobileV1\Notifications\AuditionAcceptedNotification;

class AuditionAccept
{
    public function handle(Application $application): void
    {
        if ($application->audition->status !== AuditionStatus::IN_REVIEW) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('It is not possible to change the status twice'));
        }

        $application->audition->update(['status' => AuditionStatus::ACCEPTED->value]);

        $application->role->project->user->notify(new AuditionAcceptedNotification($application));
    }
}
