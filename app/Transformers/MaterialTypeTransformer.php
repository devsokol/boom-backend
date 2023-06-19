<?php

namespace App\Transformers;

use App\Models\AttachmentType;
use League\Fractal\TransformerAbstract;

class MaterialTypeTransformer extends TransformerAbstract
{
    public function transform(AttachmentType $materialType): array
    {
        return [
            'id' => $materialType->getKey(),
            'name' => $materialType->slug,
        ];
    }
}
