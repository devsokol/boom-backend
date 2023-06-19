<?php

namespace App\Transformers;

use App\Models\RolePickShootingDate;
use League\Fractal\TransformerAbstract;

class RolePickShootingDateTransformer extends TransformerAbstract
{
    public function transform(RolePickShootingDate $rolePickShootingDate): array
    {
        return [
            'date' => $rolePickShootingDate->date,
        ];
    }
}
