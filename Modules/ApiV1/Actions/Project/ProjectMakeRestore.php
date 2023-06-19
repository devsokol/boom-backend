<?php

namespace Modules\ApiV1\Actions\Project;

use App\Enums\ProjectStatus;
use App\Models\Project;

class ProjectMakeRestore
{
    public function handle(Project $project): bool
    {
        return $project->update(['status' => ProjectStatus::ACTIVE->value]);
    }
}
