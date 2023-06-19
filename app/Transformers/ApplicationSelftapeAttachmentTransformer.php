<?php

namespace App\Transformers;

use App\Models\ApplicationSelftapeAttachment;
use App\Services\ExtensionIconService;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ApplicationSelftapeAttachmentTransformer extends TransformerAbstract
{
    public function transform(ApplicationSelftapeAttachment $applicationSelftapeAttachment): array
    {
        $data = [];

        if ($applicationSelftapeAttachment->attachment) {
            $data = [
                'id' => $applicationSelftapeAttachment->attachment->getKey(),
                'attachment' => $applicationSelftapeAttachment->attachment->attachment_repository,
                'icon_url' => (string) (new ExtensionIconService($applicationSelftapeAttachment->attachment->attachment_repository)),
            ];

            $data['basename'] = basename($applicationSelftapeAttachment->attachment);
            $data['filename'] = pathinfo($applicationSelftapeAttachment->attachment, PATHINFO_FILENAME);

            if ($applicationSelftapeAttachment->attachment->relationLoaded('attachmentType')) {
                $data['material_type'] = fractal(
                    $applicationSelftapeAttachment->attachment->attachmentType,
                    new AttachmentTypeTransformer(),
                    new ArraySerializer()
                );
            }
        }

        return $data;
    }
}
