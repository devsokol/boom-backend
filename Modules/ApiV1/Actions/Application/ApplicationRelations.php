<?php

namespace Modules\ApiV1\Actions\Application;

use App\Models\Application;
use App\Models\AttachmentType;

class ApplicationRelations
{
    public function handle(Application $application): void
    {
        $application->loadMissing([
            'actor.actorInfo.ethnicity',
            'actor.personalSkills',
            // 'actor.selftapes' => fn ($q) => $q->limit(3),
            'actor.actorAttachments' => function ($query) {
                $query
                    ->with('attachment');
                    // ->whereHas('attachment', function ($query) {
                    //     $query->where('attachment_type_id', AttachmentType::getHeadshot()->getKey());
                    // });
            },
        ]);
    }
}
