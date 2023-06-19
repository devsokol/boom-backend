<?php

namespace Modules\ApiV1\Transformers;

use App\Models\ApplicationSelftape;
use App\Transformers\ApplicationSelftapeAttachmentTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ApplicationSelftapeTransformer extends TransformerAbstract
{
    public function transform(ApplicationSelftape $applicationSelftape): array
    {
        $data = [
            'id' => $applicationSelftape->getKey(),
            'description' => $applicationSelftape->description,
            'deadline_datetime' => $applicationSelftape->deadline_datetime,
        ];

        if ($applicationSelftape->relationLoaded('applicationSelftapeAttachment')) {
            $data['application_selftape_materials'] = fractal(
                $applicationSelftape->applicationSelftapeAttachment,
                new ApplicationSelftapeAttachmentTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
