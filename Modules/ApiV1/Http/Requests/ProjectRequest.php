<?php

namespace Modules\ApiV1\Http\Requests;

use App\Rules\Base64FileSizeRule;
use App\Rules\Base64ImageMaxDimensionRule;
use App\Rules\Base64ImageRule;
use App\Rules\PreventXssRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
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
            'placeholder' => [
                'nullable',
                new Base64ImageRule(),
                new Base64FileSizeRule(config('app.max_image_filesize_kb')),
                new Base64ImageMaxDimensionRule(
                    config('app.max_image_width_px'),
                    config('app.max_image_height_px'),
                ),
            ],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('projects', 'name')->ignore($this->project),
            ],
            'description' => [
                'required',
                new PreventXssRule(),
            ],
            'start_date' => 'required|date_format:Y-m-d',
            'deadline' => 'required|date_format:Y-m-d',
            'genre_id' => 'required|exists:genres,id',
            'project_type_id' => 'nullable|exists:project_types,id',
        ];
    }
}
