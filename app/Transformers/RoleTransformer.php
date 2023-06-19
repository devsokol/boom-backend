<?php

namespace App\Transformers;

use App\Enums\ApplicationStatus;
use App\Enums\AuditionStatus;
use App\Enums\Gender;
use App\Enums\PickShootingDateType;
use App\Enums\RoleStatus;
use App\Models\Role;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class RoleTransformer extends TransformerAbstract
{
    public function __construct(private bool $expandedResponse = false)
    {
    }

    public function transform(Role $role): array
    {
        $data = [
            'id' => $role->getKey(),
            'name' => $role->name,
            'description' => $role->description,
            'acting_gender' => $role->acting_gender,
            'acting_gender_values' => Gender::values((array) $role->acting_gender),
            'min_age' => $role->min_age,
            'max_age' => $role->max_age,
            'label_range_color' => $role->label_range_color,

            'status' => $role->status,
            'status_value' => RoleStatus::from($role->status->value)->status(),

            'rate' => $role->rate,
            'city' => $role->city,
            'address' => $role->address,
            'application_deadline' => $role->application_deadline,
            'is_deprecated' => $role->isDeprecated(),
            'application_status' => null,
            'dynamic_link' => $role->dynamic_link,
        ];

        // it is for an actor
        if ($role->relationLoaded('applications') && $role->applications->isNotEmpty()) {
            $application = $role->applications->first();
            $data['application_status'] = $application->status;
            $data['application_status_value'] = ApplicationStatus::from($application->status->value)->status();

            if ($application->relationLoaded('audition') && $application->audition) {
                $audition = $application->audition;
                $data['audition_status'] = $audition->status;
                $data['audition_status_value'] = AuditionStatus::from($audition->status->value)->status();
            }
        }

        if (isset($role->actor_bookmarks_exists)) {
            $data['is_saved'] = (bool) $role->actor_bookmarks_exists;
        }

        if (isset($role->applications_exists)) {
            $data['has_applied'] = (bool) $role->applications_exists;
        }

        if ($role->relationLoaded('project')) {
            $data['project_name'] = $role->project->name;
            $data['project_genre_id'] = $role->project->genre_id;

            if ($this->expandedResponse) {
                $data['project_description'] = $role->project->description;
            }

            $data['project_type_id'] = $role->project->projectType->getKey();

            if ($role->project->relationLoaded('projectType')) {
                $data['project_type_name'] = $role->project->projectType->name;
            }

            $data['project_placeholder'] = $role->project->getPlaceholder();
            $data['project_start_date'] = $role->project->start_date;
            $data['project_deadline'] = $role->project->deadline;

            if ($role->project->relationLoaded('genre')) {
                $data['project_genre_name'] = $role->project->genre->name;
                $data['project_genre_icon'] = $role->project->genre->icon;
            }

            if ($role->project->relationLoaded('user')) {
                $data['project_email_owner'] = $role->project->user->email;
                $data['company_name'] = $role->project->user->company_name;
            }
        }

        if ($role->relationLoaded('pickShootingDates')) {
            $data['pick_shooting_date_type'] = $role->pick_shooting_date_type;
            $data['pick_shooting_date_type_value'] = $role->pick_shooting_date_type
                ? PickShootingDateType::from($role->pick_shooting_date_type->value)->types()
                : null;
            $data['pick_shooting_dates'] = fractal(
                $role->pickShootingDates,
                new RolePickShootingDateTransformer(),
                new ArraySerializer()
            );
        }

        if ($role->relationLoaded('ethnicity')) {
            $data['ethnicity'] = $role->ethnicity
                ? fractal($role->ethnicity, new EthnicityTransformer(), new ArraySerializer())
                : null;
        }

        if ($role->relationLoaded('personalSkills')) {
            $data['personal_skills'] = $role->personalSkills
                ? fractal(
                    $role->personalSkills,
                    new PersonalSkillTransformer(),
                    new ArraySerializer()
                )
                : null;
        }

        if ($role->relationLoaded('roleAttachments')) {
            $data['materials'] = fractal($role->roleAttachments, new RoleAttachmentTransformer(), new ArraySerializer());
        }

        if ($role->relationLoaded('currency')) {
            $data['currency'] = $role->currency
                ? fractal($role->currency, new CurrencyTransformer(), new ArraySerializer())
                : null;
        }

        if ($role->relationLoaded('paymentType')) {
            $data['payment_type'] = fractal($role->paymentType, new PaymentTypeTransformer(), new ArraySerializer());
        }

        if ($role->relationLoaded('country')) {
            $data['country'] = $role->country ?
                fractal($role->country, new CountryTransformer(), new ArraySerializer())
                : null;
        }

        if ($role->relationLoaded('applications')) {
            $data['applications'] = fractal($role->applications, new ApplicationTransformer(), new ArraySerializer());
        }

        return $data;
    }
}
