<?php

namespace Modules\MobileV1\Actions\Actor;

use App\Models\Actor;
use App\Models\AttachmentType;

class ActorLoadRelations
{
    public function handle(?Actor $actor): void
    {
        if ($actor) {
            $actor->load([
                'actorInfo.ethnicity',
                'actorSettings',
                'personalSkills',
                'actorAttachments' => function ($query) {
                    $query
                        ->with('attachment')
                        ->whereHas('attachment', function ($query) {
                            $query->whereIn('attachment_type_id', [
                                ...AttachmentType::getVideoTypesId(),
                                AttachmentType::getHeadshot()->getKey(),
                            ]);
                        });
                },
            ]);
        }
    }
}
