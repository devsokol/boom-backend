<?php

namespace Modules\MobileV1\Transformers;

use App\Models\ActorSetting;
use League\Fractal\TransformerAbstract;

class ActorSettingTransformer extends TransformerAbstract
{
    public function transform(ActorSetting $actorSetting): array
    {
        return [
            'allow_app_notification' => $actorSetting->allow_app_notification,
            'role_approve_notification' => $actorSetting->role_approve_notification,
            'role_reject_notification' => $actorSetting->role_reject_notification,
            'role_offer_notification' => $actorSetting->role_offer_notification,
            'audition_notification' => $actorSetting->audition_notification,
            'selftape_notification' => $actorSetting->selftape_notification,
        ];
    }
}
