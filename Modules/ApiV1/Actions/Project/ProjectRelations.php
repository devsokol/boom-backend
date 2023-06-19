<?php

namespace Modules\ApiV1\Actions\Project;

use App\Models\Project;

class ProjectRelations
{
    public function handle(Project $project): void
    {
        $project->load([
            'genre',
            'projectType',
            'roles.ethnicity',
        ]);
    }
}
