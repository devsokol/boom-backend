<?php

namespace Modules\MobileV1\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActorSettingRequest extends FormRequest
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
            'allow_app_notification' => 'sometimes|boolean',
            'role_approve_notification' => 'sometimes|boolean',
            'role_reject_notification' => 'sometimes|boolean',
            'role_offer_notification' => 'sometimes|boolean',
            'audition_notification' => 'sometimes|boolean',
            'selftape_notification' => 'sometimes|boolean',
        ];
    }
}
