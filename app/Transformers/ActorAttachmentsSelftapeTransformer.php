<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ActorAttachments;
use Spatie\Fractalistic\ArraySerializer;

class ActorAttachmentsSelftapeTransformer extends TransformerAbstract
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
            new SelftapeTransformer(),
            new ArraySerializer()
        )->toArray();
    }
}
