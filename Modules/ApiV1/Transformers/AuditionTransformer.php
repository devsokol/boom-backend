<?php

namespace Modules\ApiV1\Transformers;

use App\Enums\AuditionStatus;
use App\Enums\AuditionType;
use App\Models\Audition;
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
            'status' => $audition->status,
            'status_value' => AuditionStatus::from($audition->status->value)->status(),
            'address' => $audition->address,
            'audition_datetime' => $audition->audition_datetime,
        ];

        if ($audition->relationLoaded('auditionMaterials')) {
            $data['audition_materials'] = fractal(
                $audition->auditionMaterials,
                new AuditionMaterialTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
