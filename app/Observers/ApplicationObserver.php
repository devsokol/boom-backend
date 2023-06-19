<?php

namespace App\Observers;

use App\Events\ApplicationSaved;
use App\Events\ApplicationSaving;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class ApplicationObserver
{
    public function saving(Application $application): void
    {
        event(new ApplicationSaving($application));
    }

    public function saved(Application $application): void
    {
        event(new ApplicationSaved($application));
    }

    public function deleting(Application $application): void
    {
        $application->load('audition', 'applicationSelftape', 'recommendRole');

        DB::transaction(function () use ($application) {
            if ($application->audition) {
                $application->audition->delete();
            }

            if ($application->applicationSelftape) {
                $application->applicationSelftape->delete();
            }

            if ($application->recommendRole) {
                $application->recommendRole->delete();
            }

            $application->actorNotifications()->delete();
            $application->userNotifications()->delete();
        });
    }
}
