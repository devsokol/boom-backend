<?php

namespace Modules\MobileV1\Actions\Actor;

use App\Models\Actor;

class ActorUpdateSetting
{
    public function handle(array $data): ?Actor
    {
        return tap(auth()->user(), function ($actor) use ($data) {
            $actor->actorSettings()->updateOrCreate(['actor_id' => $actor->id], $data);
        });
    }
}
