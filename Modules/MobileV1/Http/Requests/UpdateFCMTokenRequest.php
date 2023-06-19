<?php

namespace Modules\MobileV1\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFCMTokenRequest extends FormRequest
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
            'fcm_token' => 'required|string',
        ];
    }
}
