<?php

namespace Modules\MobileV1\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadSelftapeRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        set_time_limit((int) config('app.time_limit_upload_video'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'video' => 'required|array',
            'video.*' => [
                'required',
                'file',
                'mimes:mp4,3gp,mov,avi',
                'max:' . config('app.max_video_filesize_kb'),
            ],
        ];
    }
}
