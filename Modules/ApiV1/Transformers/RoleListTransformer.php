<?php

namespace Modules\ApiV1\Transformers;

use App\Enums\ApplicationStatus;
use App\Enums\Gender;
use App\Models\Role;
use App\Transformers\ApplicationTransformer;
use App\Transformers\EthnicityTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class RoleListTransformer extends TransformerAbstract
{
    public function transform(Role $role): array
    {
        $data = [
            'id' => $role->getKey(),
            'name' => $role->name,
            'acting_gender' => $role->acting_gender,
            'acting_gender_values' => Gender::values((array) $role->acting_gender),
            'min_age' => $role->min_age,
            'max_age' => $role->max_age,
            'label_range_color' => $role->label_range_color,
            'new_applications' => $role->applications_count,
            'is_deprecated' => $role->isDeprecated(),
        ];

        $this->defineStatus($role, $data);

        if ($role->relationLoaded('userViewedApplications')) {
            if ($user = $role->userViewedApplications->first()) {
                $data['new_applications'] = abs(
                    $role->applications_count - (int) $user->pivot->amount_viewed_applications
                );
            }
        }

        if (isset($role->applications_count)) {
            $data['applications_count'] = $role->applications_count;
        }

        if (isset($role->deleted_applications_count)) {
            $data['deleted_applications_count'] = $role->deleted_applications_count;
        }

        if ($role->relationLoaded('applications')) {
            $data['applications'] = fractal(
                $role->applications,
                new ApplicationTransformer(),
                new ArraySerializer()
            );
        }

        if ($role->relationLoaded('ethnicity')) {
            $data['ethnicity'] = fractal($role->ethnicity, new EthnicityTransformer(), new ArraySerializer());
        }

        return $data;
    }

    private function defineStatus(Role $role, array &$data): void
    {
        $data['status'] = 0;
        $data['status_value'] = __('No approved');

        if (isset($role->applications_approved_count) && (int) $role->applications_approved_count > 0) {
            $data['status'] = ApplicationStatus::APPROVED->value;
            $data['status_value'] = ApplicationStatus::from(ApplicationStatus::APPROVED->value)->status();
        } elseif (isset($role->applications_approval_count) && (int) $role->applications_approval_count > 0) {
            $data['status'] = ApplicationStatus::APPROVAL->value;
            $data['status_value'] = ApplicationStatus::from(ApplicationStatus::APPROVAL->value)->status();
        } elseif (isset($role->applications_audition_count) && (int) $role->applications_audition_count > 0) {
            $data['status'] = ApplicationStatus::AUDITION->value;
            $data['status_value'] = ApplicationStatus::from(ApplicationStatus::AUDITION->value)->status();
        }
    }
}
