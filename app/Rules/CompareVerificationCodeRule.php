<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CompareVerificationCodeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        private string $userModel,
        private string $searchBy,
        private string|int $verificationCode,
    ) {
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
        $res = app($this->userModel)->where($this->searchBy, $value)->first();
        if (! $res) {
            return false;
        }

        return $res->isCodeOwner($this->verificationCode, $this->searchBy);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('User is not owner of the verification code.');
    }
}
