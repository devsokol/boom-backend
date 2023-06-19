<?php

namespace Modules\MobileV1\Actions\Application;

use Illuminate\Pagination\LengthAwarePaginator;

class ApplicationList
{
    public function handle(): LengthAwarePaginator
    {
        return auth()
            ->user()
            ->applications()
            ->with([
                'audition',
                'recommendRole',
                'applicationSelftape',
                'actor.actorInfo',
                'role.project.projectType',
                'role.project.genre',
                'role.paymentType',
                'role.currency',
            ])
            ->latest()
            ->jsonPaginate();
    }
}
