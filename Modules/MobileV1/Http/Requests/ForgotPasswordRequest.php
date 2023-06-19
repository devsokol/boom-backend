<?php

namespace Modules\MobileV1\Http\Requests;

use App\Models\Actor;
use App\Rules\ExistsUserByGatewayRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'gateway' => [
                'required',
                Rule::in(config('code_verify.verification_available_gateways')),
            ],
            'receiver' => [
                'required',
                new ExistsUserByGatewayRule(Actor::class),
            ],
        ];
    }
}
