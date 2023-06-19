<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ActorAttachments;
use Spatie\Fractalistic\ArraySerializer;

class ActorAttachmentsHeadshotTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(ActorAttachments $actorAttachment): array
    {
        return fractal(
            $actorAttachment->attachment,
            new HeadshotsTransformer(),
            new ArraySerializer()
        )->toArray();
    }
}
