<?php

namespace Modules\MobileV1\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Http\Response;

class ApplicationRejectApproval
{
    public function handle(Application $application, ?string $reason): void
    {
        if ($application->status !== ApplicationStatus::APPROVAL) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('You cannot change a status'));
        }

        $application->update([
            'status' => ApplicationStatus::REJECTED_BY_ACTOR->value,
            'reject_reason' => $reason,
        ]);
    }
}
