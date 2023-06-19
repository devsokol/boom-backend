<?php

namespace App\Transformers;

use App\Models\Attachment;
use App\Models\AttachmentType;
use League\Fractal\TransformerAbstract;

class AttachmentsTransformer extends TransformerAbstract
{
    public function transform(Attachment $attachment): array
    {
        return [
            'id' => $attachment->getKey(),
            'name' => $attachment->name,
            'mime_type' => $attachment->mime_type,
            'attachment_repository' => (in_array($attachment->attachment_type_id, AttachmentType::getVideoTypesId()))
                ? $attachment->attachment_repository->getAssets()
                : $attachment->attachment_repository,
            'attachment_type_id' => $attachment->attachment_type_id,
        ];
    }
}
