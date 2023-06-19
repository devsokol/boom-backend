<?php

namespace Modules\ApiV1\Http\Requests;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => str($this->get('email'))->lower(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'company_name' => 'nullable|string|max:100',
            'phone_number' => [
                'nullable',
                new PhoneNumberRule(),
                'unique:users',
            ],
            'email' => 'required|email|max:255|unique:users',
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
