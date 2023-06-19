<?php

namespace Modules\MobileV1\Transformers;

use App\Models\Role;
use App\Transformers\CurrencyTransformer;
use App\Transformers\PaymentTypeTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class RoleListTransformer extends TransformerAbstract
{
    public function transform(Role $role): array
    {
        $data = [
            'id' => $role->getKey(),
            'name' => $role->name,
            'rate' => $role->rate,
            'city' => $role->city,
            'address' => $role->address,
            'is_deprecated' => $role->isDeprecated(),
        ];

        if (isset($role->is_match)) {
            $data['is_match'] = (bool) $role->is_match;
        }

        if (isset($role->actor_bookmarks_exists)) {
            $data['is_saved'] = (bool) $role->actor_bookmarks_exists;
        }

        if ($role->relationLoaded('project')) {
            $data['project_name'] = $role->project->name;
            $data['project_placeholder'] = $role->project->getPlaceholder();
            $data['project_start_date'] = $role->project->start_date;
            $data['project_deadline'] = $role->project->deadline;

            if ($role->project->relationLoaded('projectType')) {
                $data['project_type_name'] = $role->project->projectType->name;
            }

            if ($role->project->relationLoaded('genre')) {
                $data['project_genre_name'] = $role->project->genre->name;
                $data['project_genre_icon'] = $role->project->genre->icon;
            }

            if ($role->project->relationLoaded('user')) {
                $data['project_email_owner'] = $role->project->user->email;
                $data['company_name'] = $role->project->user->company_name;
            }
        }

        if ($role->relationLoaded('currency')) {
            $data['currency'] = $role->currency
                ? fractal($role->currency, new CurrencyTransformer(), new ArraySerializer())
                : null;
        }

        if ($role->relationLoaded('paymentType')) {
            $data['payment_type'] = fractal($role->paymentType, new PaymentTypeTransformer(), new ArraySerializer());
        }

        return $data;
    }
}
