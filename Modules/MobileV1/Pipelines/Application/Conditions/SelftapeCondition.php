<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

use App\Models\AttachmentType;

class SelftapeCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        if (empty($handler->getActor()->actorAttachments()->exists())) {
            $handler->setRequirement(__('add at least one selftape'));
        }

        $actorAttachments = $handler
            ->getActor()
            ->actorAttachments()
            ->get();
        $issetVideo = false;

        foreach ($actorAttachments as $actorAttachment) {
            if (in_array($actorAttachment->attachment()->firstOrFail()->attachment_type_id, AttachmentType::getVideoTypesId())) {
                $issetVideo = true;
            }
        }

        if (! $issetVideo) {
            $handler->setRequirement(__('add at least one selftape'));
        }

        $next($handler);
    }
}
