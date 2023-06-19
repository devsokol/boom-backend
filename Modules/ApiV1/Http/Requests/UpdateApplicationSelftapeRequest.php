<?php

namespace Modules\ApiV1\Http\Requests;

use App\Rules\PreventXssRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationSelftapeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'required',
            'description' => [
                'max:255',
                new PreventXssRule(),
            ],
            'deadline_datetime' => 'required|date_format:Y-m-d H:i:s',
        ];
    }
}
