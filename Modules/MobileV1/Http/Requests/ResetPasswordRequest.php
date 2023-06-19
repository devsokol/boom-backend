<?php

namespace Modules\MobileV1\Http\Requests;

use App\Models\Actor;
use App\Rules\ExistsUserByGatewayRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Modules\MobileV1\Rules\CheckResetPasswordCodeRule;

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
            'gateway' => [
                'required',
                Rule::in(config('code_verify.verification_available_gateways')),
            ],
            'receiver' => [
                'required',
                new ExistsUserByGatewayRule(Actor::class),
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
}
