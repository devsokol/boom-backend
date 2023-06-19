<?php

namespace Modules\MobileV1\Actions\ApplicationSelftape;

use App\Enums\ApplicationSelftapeStatus;
use App\Models\Application;
use Illuminate\Http\Response;

class ApplicationSelftapeSended
{
    public function handle(Application $application): bool
    {
        if ($application->applicationSelftape->status !== ApplicationSelftapeStatus::IN_REVIEW) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('It is not possible to change the status twice'));
        }

        return $application->applicationSelftape->update(['status' => ApplicationSelftapeStatus::SENDED->value]);
    }
}
