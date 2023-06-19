<?php

namespace App\Transformers;

use App\Models\Attachment;
use League\Fractal\TransformerAbstract;

class HeadshotsTransformer extends TransformerAbstract
{
    public function transform(Attachment $headshot): array
    {
        return [
            'id' => $headshot->getKey(),
            'headshot' => $headshot->attachment_repository
        ];
    }
}
