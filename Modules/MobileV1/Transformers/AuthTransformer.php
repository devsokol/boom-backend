<?php

namespace Modules\MobileV1\Transformers;

use App\Models\Actor;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class AuthTransformer extends TransformerAbstract
{
    private ?Actor $actor;

    public function __construct(Actor $actor = null)
    {
        $this->actor = $actor;
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

        if ($this->actor || auth()->check()) {
            $data['data'] = fractal(
                auth()->check() ? auth()->user() : $this->actor,
                new ActorTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
