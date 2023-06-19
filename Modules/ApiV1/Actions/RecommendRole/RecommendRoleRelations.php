<?php

namespace Modules\ApiV1\Actions\RecommendRole;

use App\Models\Application;

class RecommendRoleRelations
{
    public function handle(Application $application): void
    {
        $application->recommendRole->load('role.ethnicity');
    }
}
