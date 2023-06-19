<?php

namespace Modules\ApiV1\Http\Requests;

use App\Models\MaterialType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class AuditionMaterialRequest extends FormRequest
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
        return [
            'material_type_id' => [
                'required',
                Rule::exists(MaterialType::class, 'id'),
            ],
            'attachment' => [
                'required',
                File::types(MaterialType::$allowedExtensions)->max(config('app.max_video_filesize_kb')),
            ],
        ];
    }
}
