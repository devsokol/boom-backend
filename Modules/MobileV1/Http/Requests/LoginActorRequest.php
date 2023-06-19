<?php

namespace Modules\MobileV1\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginActorRequest extends FormRequest
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
            'email' => 'required|string|email|exists:actors',
            'password' => 'required|string|min:8',
            'remember_me' => 'present|boolean',
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
