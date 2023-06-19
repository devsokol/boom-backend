<?php

namespace Modules\ApiV1\Rules;

use App\Models\VerificationCode;
use Illuminate\Contracts\Validation\Rule;

class CheckResetPasswordCodeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return VerificationCode::isVerifyCodeValid($value, 'reset-password');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Invalid code.');
    }
}
