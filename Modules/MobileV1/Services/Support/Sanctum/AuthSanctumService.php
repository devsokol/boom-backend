<?php

namespace Modules\MobileV1\Services\Support\Sanctum;

use App\Models\Actor;
use App\Services\Support\Sanctum\AbstractAuthSanctumService;
use Illuminate\Validation\ValidationException;

class AuthSanctumService extends AbstractAuthSanctumService
{
    public function __construct(
        protected string $nameToken = 'mobileToken'
    ) {
    }

    public function attempt(string $email, string $password, bool $remember = false): array
    {
        $actor = Actor::where('email', $email)->first();

        if (! $this->isPasswordMatch($actor, $password)) {
            throw ValidationException::withMessages([
                'email' => [
                    __('The email and password you’ve entered don\'t match. Please, check the data and try again.'),
                ],
            ]);
        }

        if ($actor->isMarkAsDeleted()) {
            throw ValidationException::withMessages([
                'email' => [
                    __('You can\'t enter because your account has been deleted.'),
                ],
            ]);
        }

        $tokens = $this->createPairTokens($actor, $remember);

        return [
            'tokens' => [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'access_token_expire_in' => $tokens['access_token_expire_in'],
                'refresh_token_expire_in' => $tokens['refresh_token_expire_in'],
            ],
            'user' => $actor,
        ];
    }
}
