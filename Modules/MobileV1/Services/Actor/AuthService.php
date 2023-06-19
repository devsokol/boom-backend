<?php

namespace Modules\MobileV1\Services\Actor;

use App\Models\Actor;
use App\Models\VerificationCode;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function resendVerificationCode(Actor $actor): void
    {
        if ($actor->is_account_verified) {
            throw ValidationException::withMessages([
                'code' => [
                    __('Your account has already been verified.'),
                ],
            ]);
        }

        $actor->sendVerificationCode();
    }

    public function forgotPassword(string $gateway, string $receiver): void
    {
        $actor = app(Actor::class);

        $searchField = $actor->getSearchFieldByGateway($gateway);

        $actor = $actor->where($searchField, $receiver)->first();

        $actor->sendVerificationCode(tag: 'reset-password', length: 8);
    }

    public function resetPassword(string|int $code, string $newPassword): void
    {
        $actor = VerificationCode::getVerificationModelByCode($code);

        $actor->afterSuccessfulVerification(function ($actor) use ($newPassword) {
            return $actor->update(['password' => $newPassword]);
        }, 'reset-password');
    }

    public function updateFCM(string $fcm_token): void
    {
        $actor = auth()->user();
        $actor->update(['fcm_token' => $fcm_token]);
    }
}
