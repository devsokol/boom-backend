<?php

namespace Modules\ApiV1\Services\User;

use App\Models\User;
use App\Models\VerificationCode;

class AuthService
{
    public function forgotPassword(string $email): void
    {
        $actor = User::where('email', $email)->first();

        $actor->sendVerificationCode(tag: 'reset-password', length: 8);
    }

    public function resetPassword(string|int $code, string $newPassword): void
    {
        $user = VerificationCode::getVerificationModelByCode($code);

        $user->afterSuccessfulVerification(function ($user) use ($newPassword) {
            return $user->update(['password' => $newPassword]);
        }, 'reset-password');
    }
}
