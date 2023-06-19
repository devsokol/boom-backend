<?php

namespace App\Transformers;

use App\Models\ProjectType;
use League\Fractal\TransformerAbstract;

class ProjectTypeTransformer extends TransformerAbstract
{
    public function transform(ProjectType $projectType): array
    {
        return [
            'id' => $projectType->getKey(),
            'name' => $projectType->name,
        ];
    }
}
