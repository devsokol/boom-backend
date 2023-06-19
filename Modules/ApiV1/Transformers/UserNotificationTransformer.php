<?php

namespace Modules\ApiV1\Transformers;

use App\Enums\UserNotificationStatus;
use App\Models\UserNotification;
use League\Fractal\TransformerAbstract;

class UserNotificationTransformer extends TransformerAbstract
{
    public function transform(UserNotification $userNotification): array
    {
        $data = [
            'id' => $userNotification->getKey(),
            'title' => $userNotification->title,
            'body' => $userNotification->body,
            'is_read' => $userNotification->is_read,
            'status' => $userNotification->status,
            'status_value' => UserNotificationStatus::from($userNotification->status->value)->status(),
            'application_id' => $userNotification->application_id,
        ];

        if ($userNotification->relationLoaded('application')) {
            if ($userNotification->application->relationLoaded('role')
                && $userNotification->application->role->id) {
                $data['role_id'] = $userNotification->application->role->id;

                if ($userNotification->application->role->relationLoaded('project')) {
                    $data['placeholder'] = $userNotification->application->role->project->getPlaceholder();
                }
            }
        }

        return $data;
    }
}
