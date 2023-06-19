<?php

namespace App\Transformers;

use App\Models\RoleMaterial;
use App\Services\ExtensionIconService;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class RoleMaterialTransformer extends TransformerAbstract
{
    public function transform(RoleMaterial $roleMaterial): array
    {
        $data = [
            'id' => $roleMaterial->getKey(),
            'attachment' => $roleMaterial->attachment,
            'icon_url' => (string) (new ExtensionIconService($roleMaterial->attachment)),
        ];

        if ($roleMaterial->attachment) {
            $data['basename'] = basename($roleMaterial->attachment);
            $data['filename'] = pathinfo($roleMaterial->attachment, PATHINFO_FILENAME);
        }

        if ($roleMaterial->relationLoaded('materialType')) {
            $data['material_type'] = fractal(
                $roleMaterial->materialType,
                new MaterialTypeTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
