<?php

namespace Modules\ApiV1\Actions\Project;

use App\Enums\ProjectStatus;
use App\Jobs\ProjectSmoothDeleting;
use App\Models\Project;

class ProjectDelete
{
    public function handle(Project $project): bool
    {
        $result = $project->update(['status' => ProjectStatus::DELETED->value]);

        ProjectSmoothDeleting::dispatch($project)
            ->delay(now()->addMonths(3));

        return $result;
    }
}
