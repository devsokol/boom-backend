<?php

namespace Modules\ApiV1\Http\Requests;

use App\Models\AttachmentType;
use App\Rules\PreventXssRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ApplicationSelftapeRequest extends FormRequest
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
            'description' => [
                'required',
                'max:255',
                new PreventXssRule(),
            ],
            'deadline_datetime' => 'required|date_format:Y-m-d H:i',
            'materials' => 'nullable|array',
            'materials.*.material_type_id' => [
                'required',
                Rule::exists(AttachmentType::class, 'id'),
            ],
            'materials.*.attachment' => [
                'required',
                File::types(AttachmentType::getAllowedExtensionsFlat())->max(config('app.max_video_filesize_kb')),
            ],
        ];
    }
}
