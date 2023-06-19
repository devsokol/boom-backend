<?php

namespace Modules\ApiV1\Actions\ApplicationSelftape;

use App\Models\Application;

class ApplicationSelftapeUpdate
{
    public function handle(Application $application, array $data): bool
    {
        return $application->applicationSelftape->update($data);
    }
}
