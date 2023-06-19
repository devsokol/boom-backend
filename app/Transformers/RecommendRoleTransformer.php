<?php

namespace App\Transformers;

use App\Enums\RecommendRoleStatus;
use App\Models\RecommendRole;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class RecommendRoleTransformer extends TransformerAbstract
{
    public function transform(RecommendRole $recommendRole): array
    {
        $data = [
            'id' => $recommendRole->getKey(),
            'status' => $recommendRole->status,
            'status_value' => RecommendRoleStatus::from($recommendRole->status->value)->status(),
        ];

        if ($recommendRole->relationLoaded('role')) {
            $data['role'] = fractal($recommendRole->role, new RoleTransformer(true), new ArraySerializer());
        }

        return $data;
    }
}
