<?php

namespace Modules\ApiV1\Actions\Project;

use App\Models\Project;

class ProjectStore
{
    public function handle(array $data): Project
    {
        return auth()->user()->projects()->create($data);
    }
}
