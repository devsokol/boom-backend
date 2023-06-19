<?php

namespace Modules\ApiV1\Transformers;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Transformers\RoleTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ProjectListTransformer extends TransformerAbstract
{
    public function transform(Project $project): array
    {
        $data = [
            'id' => $project->getKey(),
            'name' => $project->name,
            'status' => $project->status,
            'status_value' => ProjectStatus::from($project->status->value)->status(),
        ];

        if ($project->relationLoaded('roles')) {
            $data['roles'] = fractal($project->roles, new RoleTransformer(), new ArraySerializer());
        }

        return $data;
    }
}
