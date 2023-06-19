<?php

namespace Modules\ApiV1\Actions\Project;

use Illuminate\Support\Collection;

class ProjectList
{
    public function handle(array $filter): Collection
    {
        return auth()
            ->user()
            ->projects()
            ->with([
                'genre',
                'projectType',
                'roles.ethnicity',
            ])
            ->filter($filter)
            ->latest('id')
            ->get();
    }
}
