<?php

namespace Modules\MobileV1\Transformers;

use App\Enums\ActorNotificationStatus;
use App\Models\ActorNotification;
use League\Fractal\TransformerAbstract;

class ActorNotificationTransformer extends TransformerAbstract
{
    public function transform(ActorNotification $actorNotification): array
    {
        $data = [
            'id' => $actorNotification->getKey(),
            'title' => $actorNotification->title,
            'body' => $actorNotification->body,
            'is_read' => $actorNotification->is_read,
            'status' => $actorNotification->status,
            'status_value' => ActorNotificationStatus::from($actorNotification->status->value)->status(),
            'application_id' => $actorNotification->application_id,
        ];

        if ($actorNotification->relationLoaded('application')) {
            if ($actorNotification->application->relationLoaded('role')
                && $actorNotification->application->role->id) {
                $data['role_id'] = $actorNotification->application->role->id;

                if ($actorNotification->application->role->relationLoaded('project')) {
                    $data['placeholder'] = $actorNotification->application->role->project->getPlaceholder();
                }
            }
        }

        return $data;
    }
}
