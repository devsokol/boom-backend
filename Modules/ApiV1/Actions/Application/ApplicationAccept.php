<?php

namespace Modules\ApiV1\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Http\Response;

class ApplicationAccept
{
    public function handle(Application $application): bool
    {
        if ($application->status === ApplicationStatus::APPROVED) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('The actor has already been accepted'));
        }

        return $application->update(['status' => ApplicationStatus::APPROVAL->value]);
    }
}
