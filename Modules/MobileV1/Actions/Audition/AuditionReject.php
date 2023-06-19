<?php

namespace Modules\MobileV1\Actions\Audition;

use App\Enums\AuditionStatus;
use App\Models\Application;
use Illuminate\Http\Response;
use Modules\MobileV1\Notifications\AuditionDeclinedNotification;

class AuditionReject
{
    public function handle(Application $application, ?string $reason): void
    {
        if ($application->audition->status !== AuditionStatus::IN_REVIEW) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('It is not possible to change the status twice'));
        }

        $application->audition->update([
            'status' => AuditionStatus::REJECTED->value,
            'reject_reason' => $reason,
        ]);

        $application->role->project->user->notify(new AuditionDeclinedNotification($application));
    }
}
