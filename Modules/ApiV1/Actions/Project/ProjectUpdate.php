<?php

namespace Modules\ApiV1\Actions\Project;

use App\Models\Project;

class ProjectUpdate
{
    public function handle(Project $project, array $data): bool
    {
        return $project->update($data);
    }
}
