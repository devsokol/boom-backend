<?php

namespace Modules\ApiV1\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Http\Response;

class ApplicationReject
{
    public function handle(Application $application): bool
    {
        if ($application->status === ApplicationStatus::APPROVED) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('You cannot cancel the decision'));
        }

        return $application->update(['status' => ApplicationStatus::REJECTED_BY_OWNER->value]);
    }
}
