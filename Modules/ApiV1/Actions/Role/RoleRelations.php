<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Role;

class RoleRelations
{
    public function handle(Role $role): void
    {
        $role->load([
            'ethnicity',
            'personalSkills',
            'roleAttachments',
            'roleAttachments.attachment',
            'roleAttachments.attachment.attachmentType',
            'pickShootingDates',
            'currency',
            'paymentType',
            'country',
            'project.user',
            'project.genre',
            'project.projectType',
        ]);
    }
}
