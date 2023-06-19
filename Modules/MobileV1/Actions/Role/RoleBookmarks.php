<?php

namespace Modules\MobileV1\Actions\Role;

use Illuminate\Pagination\LengthAwarePaginator;

class RoleBookmarks
{
    public function handle(): LengthAwarePaginator
    {
        return auth()
            ->user()
            ->roleBookmarks()
            ->with([
                'ethnicity',
                'personalSkills',
                'roleMaterials',
                'roleMaterials.materialType',
                'pickShootingDates',
                'currency',
                'paymentType',
                'country',
                'project.user',
                'project.genre',
                'project.projectType',
            ])
            ->public()
            ->onlyActiveProject()
            ->jsonPaginate();
    }
}
