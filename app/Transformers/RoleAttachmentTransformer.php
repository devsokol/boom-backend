<?php

namespace App\Transformers;

use App\Models\RoleAttachment;
use App\Services\ExtensionIconService;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class RoleAttachmentTransformer extends TransformerAbstract
{
    public function transform(RoleAttachment $roleAttachment): array
    {
        $data = [
            'id' => $roleAttachment->attachment->getKey(),
            'attachment' => $roleAttachment->attachment->attachment_repository,
            'icon_url' => (string) (new ExtensionIconService($roleAttachment->attachment->attachment_repository)),
        ];

        if ($roleAttachment->attachment) {
            $data['basename'] = basename($roleAttachment->attachment);
            $data['filename'] = pathinfo($roleAttachment->attachment, PATHINFO_FILENAME);
        }

        if ($roleAttachment->attachment->relationLoaded('attachmentType')) {
            $data['material_type'] = fractal(
                $roleAttachment->attachment->attachmentType,
                new AttachmentTypeTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
