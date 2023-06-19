<?php

namespace App\Transformers;

use App\Models\ApplicationSelftapeMaterial;
use App\Services\ExtensionIconService;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ApplicationSelftapeMaterialTransformer extends TransformerAbstract
{
    public function transform(ApplicationSelftapeMaterial $applicationSelftapeMaterial): array
    {
        $data = [
            'id' => $applicationSelftapeMaterial->getKey(),
            'attachment' => $applicationSelftapeMaterial->attachment,
            'icon_url' => (string) (new ExtensionIconService($applicationSelftapeMaterial->attachment)),
        ];

        if ($applicationSelftapeMaterial->attachment) {
            $data['basename'] = basename($applicationSelftapeMaterial->attachment);
            $data['filename'] = pathinfo($applicationSelftapeMaterial->attachment, PATHINFO_FILENAME);
        }

        if ($applicationSelftapeMaterial->relationLoaded('materialType')) {
            $data['material_type'] = fractal(
                $applicationSelftapeMaterial->materialType,
                new MaterialTypeTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
