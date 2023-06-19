<?php

namespace Modules\MobileV1\Http\Requests;

use App\Models\AttachmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UploadAttachmentRequest extends FormRequest
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
            'attachments' => 'required|array',
            'attachments.*.file' => [
                'nullable',
                'required_without:attachments.*.link',
                File::types(AttachmentType::getAllowedExtensionsFlat()),
                'max:10240', // 10 MB max file size
            ],
            'attachments.*.link' => [
                'nullable',
                'required_without:attachments.*.file',
                'string',
                'max:300',
            ],
            'attachments.*.type' => [
                'required',
                'string',
                Rule::exists(AttachmentType::class, 'name'),
            ],
            'attachments.*.description' => 'nullable|string',
            'save_to_profile' => 'nullable|boolean',
        ];
    }
}
