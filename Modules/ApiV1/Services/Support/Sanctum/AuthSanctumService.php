<?php

namespace Modules\ApiV1\Services\Support\Sanctum;

use App\Models\User;
use App\Services\Support\Sanctum\AbstractAuthSanctumService;
use Illuminate\Validation\ValidationException;

class AuthSanctumService extends AbstractAuthSanctumService
{
    public function __construct(
        protected string $nameToken = 'apiToken'
    ) {
    }

    public function attempt(string $email, string $password, bool $remember = false): array
    {
        $user = User::where('email', $email)->first();

        if (! $this->isPasswordMatch($user, $password)) {
            throw ValidationException::withMessages([
                'email' => [
                    __('The email and password you’ve entered don\'t match. Please, check the data and try again.'),
                ],
            ]);
        }

        $tokens = $this->createPairTokens($user, $remember);

        return [
            'tokens' => [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'access_token_expire_in' => $tokens['access_token_expire_in'],
                'refresh_token_expire_in' => $tokens['refresh_token_expire_in'],
            ],
            'user' => $user,
        ];
    }
}
