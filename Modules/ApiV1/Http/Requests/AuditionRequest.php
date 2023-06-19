<?php

namespace Modules\ApiV1\Http\Requests;

use App\Enums\AuditionType;
use App\Models\MaterialType;
use App\Rules\PreventXssRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

class AuditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        set_time_limit((int) config('app.time_limit_upload_video'));
    }

    public function rules(): array
    {
        $data = [
            'address' => [
                'required',
                'max:255',
                new PreventXssRule(),
            ],
            'type' => [
                'required',
                new Enum(AuditionType::class),
            ],
            'audition_date' => 'required|date_format:Y-m-d',
            'audition_time' => 'required|date_format:H:i',
        ];

        if ($this->isMethod('post') && $this->get('_method') !== 'PUT') {
            $data['materials'] = 'nullable|array';
            $data['materials.*.material_type_id'] = [
                'sometimes',
                Rule::exists(MaterialType::class, 'id'),
            ];
            $data['materials.*.attachment'] = [
                'sometimes',
                File::types(MaterialType::$allowedExtensions)->max(config('app.max_video_filesize_kb')),
            ];
        }

        return $data;
    }

    public function validated($key = null, $default = null)
    {
        $formattedData = parent::validated($key, $default);

        $formattedData['audition_datetime'] = Carbon::parse(
            $formattedData['audition_date'] . ' ' . $formattedData['audition_time']
        );

        unset($formattedData['audition_date']);
        unset($formattedData['audition_time']);

        return $formattedData;
    }
}
