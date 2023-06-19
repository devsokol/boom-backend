<?php

namespace Modules\ApiV1\Transformers;

use App\Models\AuditionMaterial;
use App\Transformers\MaterialTypeTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class AuditionMaterialTransformer extends TransformerAbstract
{
    public function transform(AuditionMaterial $auditionMaterial): array
    {
        $data = [
            'id' => $auditionMaterial->getKey(),
            'attachment' => $auditionMaterial->attachment,
        ];

        if ($auditionMaterial->attachment) {
            $data['basename'] = basename($auditionMaterial->attachment);
            $data['filename'] = pathinfo($auditionMaterial->attachment, PATHINFO_FILENAME);
        }

        if ($auditionMaterial->relationLoaded('materialType')) {
            $data['material_type'] = fractal(
                $auditionMaterial->materialType,
                new MaterialTypeTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
