<?php

namespace Modules\ApiV1\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class AuthTransformer extends TransformerAbstract
{
    private ?User $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    public function transform(array $params): array
    {
        $data = [
            'access_token' => $params['access_token'],
            'refresh_token' => $params['refresh_token'],
            'token_type' => 'bearer',
            'access_token_expire_in' => $params['access_token_expire_in'],
            'refresh_token_expire_in' => $params['refresh_token_expire_in'],
        ];

        if ($this->user || auth()->check()) {
            $data['data'] = fractal(
                auth()->check() ? auth()->user() : $this->user,
                new UserTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
