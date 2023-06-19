<?php

namespace App\Transformers;

use App\Models\Attachment;
use App\Models\AttachmentType;
use League\Fractal\TransformerAbstract;

class SelftapeTransformer extends TransformerAbstract
{
    public function transform(Attachment $selftape): array
    {
        return [
            'id' => $selftape->getKey(),
            'video' => $selftape->attachment_repository->getAssets(),
            'description' => $selftape->description,
            'type' => AttachmentType::find($selftape->attachment_type_id)->name,
            'mime_type' => $selftape->mime_type,
        ];
    }
}
