<?php

namespace Modules\ApiV1\Http\Requests;

use App\Enums\Gender;
use App\Enums\PickShootingDateType;
use App\Enums\RoleStatus;
use App\Models\AttachmentType;
use App\Models\Country;
use App\Models\PersonalSkill;
use App\Rules\PreventXssRule;
use App\Services\Common\PaymentType\PaymentTypeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

class RoleRequest extends FormRequest
{
    /**
     * When a isSinglePaymentType property is set to TRUE,
     * then the fields: rate, currency_id are will be null.
     */
    private bool $isSinglePaymentType = false;

    private string $rawCountryValue;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'rate' => (int) $this->get('rate'),
            'status' => (int) $this->get('status'),
        ]);

        $this->rawCountryValue = $this->get('country_id') ?? '';

        if (! empty($this->get('country_id'))) {
            $this->merge(['country_id' => $this->getCountryIdByByName($this->rawCountryValue)]);
        }

        if ($paymentTypeId = $this->get('payment_type_id')) {
            $this->isSinglePaymentType = PaymentTypeService::getValueIsSingleByPaymentTypeId($paymentTypeId);

            if ($this->isSinglePaymentType) {
                $this->merge([
                    'currency_id' => null,
                    'rate' => null,
                ]);
            }
        }

        set_time_limit((int) config('app.time_limit_upload_video'));
    }

    public function rules(): array
    {
        $data = [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'description' => [
                'required',
                new PreventXssRule(),
            ],
            'acting_gender' => 'nullable|array',
            'acting_gender.*' => [
                'sometimes',
                'nullable',
                new Enum(Gender::class),
            ],
            'min_age' => 'sometimes|nullable|integer|min:0|max:130',
            'max_age' => 'sometimes|nullable|integer|gt:min_age|max:130',
            'personal_skills' => 'nullable|array',
            'personal_skills.*' => [
                'nullable',
                Rule::exists(PersonalSkill::class, 'id'),
            ],
            'status' => [
                'nullable',
                new Enum(RoleStatus::class),
            ],
            'rate' => 'required|integer|max:999999999',
            'city' => 'sometimes|max:255',
            'address' => 'sometimes|max:255',
            'application_deadline' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'pick_shooting_date_type' => [
                'nullable',
                new Enum(PickShootingDateType::class),
            ],
            'pick_shooting_dates' => 'sometimes|array',
            'pick_shooting_dates.*.date' => 'date_format:Y-m-d',
            'currency_id' => 'required|exists:currencies,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'ethnicity_id' => 'nullable|exists:ethnicities,id',
            'country_id' => 'nullable|exists:countries,id',
        ];

        if ($this->isSinglePaymentType) {
            $data['currency_id'] = 'nullable';
            $data['rate'] = 'nullable';
        }

        if ($this->isMethod('post') && $this->get('_method') !== 'PUT') {
            $data['materials'] = 'nullable|array';
            $data['materials.*.material_type_id'] = [
                'sometimes',
                Rule::exists(AttachmentType::class, 'id'),
            ];
            $data['materials.*.attachment'] = [
                'sometimes',
                File::types(AttachmentType::getAllowedExtensionsFlat())->max(config('app.max_video_filesize_kb')),
            ];
        }

        return $data;
    }

    /**
     * @todo will need to remove this in the future
     */
    private function getCountryIdByByName(string $name): int
    {
        $country = Country::where('name', 'ilike', $name)->first();

        if ($country) {
            return $country->getKey();
        }

        return 0;
    }

    public function messages()
    {
        return [
            'application_deadline.required_if' => __(
                'The application deadline field is required when status is PUBLIC',
            ),
            'country_id.exists' => __(
                'The country :value was not found in the database',
                [
                    'value' => $this->rawCountryValue,
                ]
            ),
        ];
    }
}
