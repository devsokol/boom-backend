<?php

namespace Modules\ApiV1\Actions\Application;

use App\Models\Application;
use Illuminate\Http\Response;

class ApplicationCheckingWhetherRequestIsAllowed
{
    public function handle(Application $application): void
    {
        if ($application->isActorDeleted()) {
            abort(
                Response::HTTP_FAILED_DEPENDENCY,
                __('The operation cannot be completed because the actor has been deleted')
            );
        }
    }
}
