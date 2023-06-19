<?php

namespace Modules\ApiV1\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return [
            'id' => $user->getKey(),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'avatar' => $user->avatar,
            'company_name' => $user->company_name,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
