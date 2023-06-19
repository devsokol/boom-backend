<?php

namespace Modules\MobileV1\Actions\Role;

use App\Models\Role;

class RoleRelations
{
    public function handle(Role $role): void
    {
        $role->load([
            'applications',
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

        $actorId = auth()->user()->getKey();

        $role->loadExists([
            'actorBookmarks' => fn ($q) => $q->whereActorId($actorId),
            'applications' => fn ($q) => $q->whereActorId($actorId),
        ]);
    }
}
