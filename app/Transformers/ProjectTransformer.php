<?php

namespace App\Transformers;

use App\Enums\ProjectStatus;
use App\Models\Project;
use League\Fractal\TransformerAbstract;
use Modules\ApiV1\Transformers\UserTransformer;
use Spatie\Fractalistic\ArraySerializer;

class ProjectTransformer extends TransformerAbstract
{
    public function transform(Project $project): array
    {
        $data = [
            'id' => $project->getKey(),
            'placeholder' => $project->getPlaceholder(),
            'name' => $project->name,
            'description' => $project->description,
            'start_date' => $project->start_date,
            'deadline' => $project->deadline,
            'status' => $project->status,
            'status_value' => ProjectStatus::from($project->status->value)->status(),
        ];

        if ($project->relationLoaded('genre')) {
            $data['genre'] = fractal($project->genre, new GenreTransformer(), new ArraySerializer());
        }

        if ($project->relationLoaded('projectType')) {
            $data['project_type'] = fractal($project->projectType, new ProjectTypeTransformer(), new ArraySerializer());
        }

        if ($project->relationLoaded('roles')) {
            $data['roles'] = fractal($project->roles, new RoleTransformer(), new ArraySerializer());
        }

        if ($project->relationLoaded('user')) {
            $data['user'] = fractal($project->user, new UserTransformer(), new ArraySerializer());
        }

        return $data;
    }
}
