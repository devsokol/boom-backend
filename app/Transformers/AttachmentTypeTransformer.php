<?php

namespace App\Transformers;

use App\Models\AttachmentType;
use League\Fractal\TransformerAbstract;

class AttachmentTypeTransformer extends TransformerAbstract
{
    public function transform(AttachmentType $attachmentType): array
    {
        return [
            'id' => $attachmentType->getKey(),
            'name' => $attachmentType->slug,
        ];
    }
}
