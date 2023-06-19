<?php

namespace Modules\MobileV1\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Http\Response;

class ApplicationAcceptApproval
{
    public function handle(Application $application): void
    {
        if ($application->status !== ApplicationStatus::APPROVAL) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('You cannot change a status'));
        }

        $application->update(['status' => ApplicationStatus::APPROVED->value]);
    }
}
