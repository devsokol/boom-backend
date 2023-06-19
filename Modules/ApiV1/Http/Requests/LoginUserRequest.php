<?php

namespace Modules\ApiV1\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
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
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string|min:8',
            'remember_me' => 'present|boolean',
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => __(
                'You are not a member of the :app. If you wish to become a :app member, visit the Sign-up page.',
                [
                    'app' => config('app.name'),
                ]
            ),
        ];
    }
}
