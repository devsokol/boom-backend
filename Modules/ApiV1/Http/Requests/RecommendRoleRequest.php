<?php

namespace Modules\ApiV1\Http\Requests;

use App\Rules\PreventIdenticalRecommendRoleRule;
use Illuminate\Foundation\Http\FormRequest;

class RecommendRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id' => [
                'required',
                'exists:roles,id',
                new PreventIdenticalRecommendRoleRule($this->application),
            ],
        ];
    }
}
