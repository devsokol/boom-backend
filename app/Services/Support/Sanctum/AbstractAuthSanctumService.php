<?php

namespace App\Services\Support\Sanctum;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

abstract class AbstractAuthSanctumService
{
    public function __construct(
        protected string $nameToken = 'auth'
    ) {
    }

    abstract public function attempt(string $email, string $password, bool $remember = false): array;

    public function isPasswordMatch(?Model $user, string $requestPassword): bool
    {
        if (! $user) {
            return false;
        }
        /* @phpstan-ignore-next-line */
        return Hash::check($requestPassword, $user?->password);
    }

    public function createToken(Model $user, DateTimeInterface $expiresAt = null, array $abilities = []): string|null
    {
        /* @phpstan-ignore-next-line */
        return $user ? $user?->createToken($this->nameToken, $abilities, $expiresAt)?->plainTextToken : null;
    }

    public function createPairTokens(Model $user, bool $remember = false): array
    {
        if ((bool) config('sanctum.delete_previous_tokens')) {
            try {
                $user->tokens()->delete();
            } catch (\Exception $e) {
                \Log::critical($e->getMessage());
            }
        }

        $expireAccessTokenAt = $this->expirationAccessTokenAt($remember);
        $expireRefreshTokenAt = $this->expirationRefreshTokenAt($remember);

        return [
            'access_token' => $this->createToken($user, $expireAccessTokenAt, ['auth-token']),
            'refresh_token' => $this->createToken($user, $expireRefreshTokenAt, ['refresh-token']),
            'access_token_expire_in' => $expireAccessTokenAt,
            'refresh_token_expire_in' => $expireRefreshTokenAt,
        ];
    }

    private function expirationAccessTokenAt(bool $remember = false): Carbon
    {
        return $remember
            ? now()->addMinutes(config('sanctum.access_token_remember_me_expiration'))
            : now()->addMinutes(config('sanctum.access_token_expiration'));
    }

    private function expirationRefreshTokenAt(bool $remember = false): Carbon
    {
        return $remember
            ? now()->addMinutes(config('sanctum.refresh_token_remember_me_expiration'))
            : now()->addMinutes(config('sanctum.refresh_token_expiration'));
    }
}
