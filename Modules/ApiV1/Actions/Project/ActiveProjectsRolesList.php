<?php

namespace Modules\ApiV1\Actions\Project;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Collection;

class ActiveProjectsRolesList
{
    public function handle(): Collection
    {
        return auth()
            ->user()
            ->projects()
            ->select('id', 'name', 'status')
            ->active()
            ->whereHas('roles', function ($q) {
                $q->whereDoesntHave('applications', function ($q) {
                    $q->whereIn('status', [
                        ApplicationStatus::APPROVED->value,
                        ApplicationStatus::APPROVAL->value,
                    ]);
                });
            })
            ->with([
                'roles.ethnicity',
                'roles' => function ($q) {
                    $q->whereDoesntHave('applications', function ($q) {
                        $q->whereIn('status', [
                            ApplicationStatus::APPROVED->value,
                            ApplicationStatus::APPROVAL->value,
                        ]);
                    });
                },
            ])
            ->latest('id')
            ->get();
    }
}
