<?php

namespace Modules\MobileV1\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MinMaxPriceRequest extends FormRequest
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
            'currency_id' => 'required|exists:currencies,id',
            'payment_type_id' => 'required|exists:payment_types,id',
        ];
    }
}
