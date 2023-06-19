<?php

namespace Modules\ApiV1\Http\Requests;

use App\Models\User;
use App\Rules\CompareVerificationCodeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Modules\ApiV1\Rules\CheckResetPasswordCodeRule;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'code' => [
                'required',
                new CheckResetPasswordCodeRule(),
            ],
            'email' => [
                'required',
                'email',
                'exists:users,email',
                new CompareVerificationCodeRule(User::class, searchBy: 'email', verificationCode: $this->code),
            ],
            'password' => [
                'required',
                'confirmed',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => __('The email you’ve entered does not belong to any BOOM member. '
                .'If you wish to join the Boom, visit the Sign-up page.'),
        ];
    }
}
