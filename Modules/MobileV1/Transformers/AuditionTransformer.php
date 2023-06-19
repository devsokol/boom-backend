<?php

namespace Modules\MobileV1\Transformers;

use App\Enums\AuditionStatus;
use App\Enums\AuditionType;
use App\Models\Audition;
use App\Transformers\AuditionMaterialTransformer;
use App\Transformers\RoleMaterialTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class AuditionTransformer extends TransformerAbstract
{
    public function transform(Audition $audition): array
    {
        $data = [
            'id' => $audition->getKey(),
            'type' => $audition->type,
            'type_value' => AuditionType::from($audition->type->value)->types(),
            'address' => $audition->address,
            'reject_reason' => $audition->reject_reason,
            'status' => $audition->status,
            'status_value' => AuditionStatus::from($audition->status->value)->status(),
            'audition_datetime' => $audition->audition_datetime,
            'audition_date' => $audition->audition_datetime->format('d.m.Y'),
            'audition_time' => $audition->audition_datetime->format('H:i'),
        ];

        if ($audition->relationLoaded('application') && $audition->application->relationLoaded('role')) {
            $data['audition_info']['role_name'] = $audition->application->role->name ?? null;

            if ($audition->application->role->relationLoaded('project')) {
                $data['audition_info']['film_name'] = $audition->application->role->project->name ?? null;
            }

            if ($audition->application->role->relationLoaded('roleMaterials')) {
                $data['default_preparation_materials'] = $audition->application->role->roleMaterials
                    ? fractal(
                        $audition->application->role->roleMaterials,
                        new RoleMaterialTransformer(),
                        new ArraySerializer()
                    )
                    : null;
            }
        }

        if ($audition->relationLoaded('auditionMaterials')) {
            $data['audition_preparation_materials'] = $audition->auditionMaterials
                ? fractal($audition->auditionMaterials, new AuditionMaterialTransformer(), new ArraySerializer())
                : null;
        }

        return $data;
    }
}
