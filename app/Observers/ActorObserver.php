<?php

namespace App\Observers;

use App\Models\Actor;
use Illuminate\Support\Facades\DB;
use Modules\ApiV1\Actions\Role\RoleChangeAmountOfViewedApplications;

class ActorObserver
{
    public function deleting(Actor $actor): void
    {
        $actor->load('headshots', 'selftapes', 'applications.role');

        DB::transaction(function () use ($actor) {
            $actor->actorNotifications()->delete();
            $actor->roleBookmarks()->sync([]);
            $actor->personalSkills()->sync([]);
            $actor->tokens()->delete();
            $actor->codes()->delete();

            $actor->applications->each(function ($application) {
                (new RoleChangeAmountOfViewedApplications)->handle($application->role);
            });

            if ($actor->actorSettings) {
                $actor->actorSettings?->delete();
            }

            if ($actor->actorInfo) {
                $actor->actorInfo?->delete();
            }

            // TODO
            $actor->headshots->each->delete();
            $actor->selftapes->each->delete();
        });
    }
}
