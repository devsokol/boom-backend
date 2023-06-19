<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

use App\Models\AttachmentType;

class HeadshotCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        if (empty($handler->getActor()->actorAttachments()->exists())) {
            $handler->setRequirement(__('add a headshot'));
        }

        $actorAttachments = $handler
            ->getActor()
            ->actorAttachments()
            ->get();
        $issetHeadshot = false;

        foreach ($actorAttachments as $actorAttachment) {
            if ($actorAttachment->attachment()->firstOrFail()->attachment_type_id === AttachmentType::getType('headshot')->getKey()) {
                $issetHeadshot = true;
            }
        }

        if (! $issetHeadshot) {
            $handler->setRequirement(__('add a headshot'));
        }

        $next($handler);
    }
}
