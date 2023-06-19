<?php

namespace Modules\MobileV1\Http\Requests;

use App\Enums\Gender;
use App\Rules\Base64FileSizeRule;
use App\Rules\Base64ImageMaxDimensionRule;
use App\Rules\Base64ImageRule;
use App\Rules\LinkContainDomainRule;
use App\Rules\PreventXssRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProfileRequest extends FormRequest
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
        $this->merge([
            'ethnicity_id' => $this->ethnicity_id ? (int) $this->ethnicity_id : null,
            'skill_list' => (array) $this->skill_list,
        ]);

        set_time_limit((int) config('app.time_limit_upload_video'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'bio' => [
                'nullable',
                'string',
                'max:255',
                new PreventXssRule(),
            ],
            'city' => 'nullable|string|max:100',
            'ethnicity_id' => 'nullable|exists:ethnicities,id',
            'acting_gender' => 'nullable|array',
            'acting_gender.*' => [
                'sometimes',
                'nullable',
                new Enum(Gender::class),
            ],
            'min_age' => 'nullable|required_with:max_age|integer|min:1|max:199',
            'max_age' => 'nullable|required_with:min_age|integer|min:1|max:199',
            'pseudonym' => 'nullable|string|max:100',
            'skill_list' => 'nullable|array',
            'skill_list.*' => 'required|exists:personal_skills,id',
            'behance_link' => [
                'nullable',
                new LinkContainDomainRule(['be.net', 'behance.net']),
            ],
            'instagram_link' => [
                'nullable',
                new LinkContainDomainRule(['instagram.com']),
            ],
            'youtube_link' => [
                'nullable',
                new LinkContainDomainRule(['youtube.com', 'youtu.be']),
            ],
            'facebook_link' => [
                'nullable',
                new LinkContainDomainRule(['fb.me', 'fb.com', 'facebook.com']),
            ],
            'headshots' => 'nullable|array',
            'headshots.*' => [
                'required',
                new Base64ImageRule(),
                new Base64FileSizeRule(config('app.max_image_filesize_kb')),
                new Base64ImageMaxDimensionRule(
                    config('app.max_image_width_px'),
                    config('app.max_image_height_px'),
                ),
            ],
            'selftapes' => 'nullable|array',
            'selftapes.*' => [
                'required',
                'file',
                'mimes:mp4,3gp,mov,avi',
                'max:' . config('app.max_video_filesize_kb'),
            ],
        ];
    }
}
