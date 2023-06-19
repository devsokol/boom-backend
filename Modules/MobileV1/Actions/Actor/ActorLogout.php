<?php

namespace Modules\MobileV1\Actions\Actor;

use App\Models\Actor;

class ActorLogout
{
    public function handle(?Actor $actor): void
    {
        if ($actor) {
            $actor->update(['fcm_token' => null]);
            $actor->tokens()->delete();
        }
    }
}
