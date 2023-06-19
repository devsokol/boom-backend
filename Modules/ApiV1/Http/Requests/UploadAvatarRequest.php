<?php

namespace Modules\ApiV1\Http\Requests;

use App\Rules\Base64FileSizeRule;
use App\Rules\Base64ImageMaxDimensionRule;
use App\Rules\Base64ImageRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadAvatarRequest extends FormRequest
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
            'avatar' => [
                'required',
                new Base64ImageRule(),
                new Base64FileSizeRule(config('app.max_image_filesize_kb')),
                new Base64ImageMaxDimensionRule(
                    config('app.max_image_width_px'),
                    config('app.max_image_height_px'),
                ),
            ],
        ];
    }
}
