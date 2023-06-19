<?php

namespace Modules\MobileV1\Actions\Application;

use App\Models\Application;

class ApplicationRelations
{
    public function handle(Application $application): void
    {
        $application->load([
            'audition',
            'recommendRole.role.project.genre',
            'recommendRole.role.project.user',
            'recommendRole.role.currency',
            'recommendRole.role.ethnicity',
            'recommendRole.role.personalSkills',
            'recommendRole.role.pickShootingDates',
            'recommendRole.role.roleAttachments.attachment',
            'recommendRole.role.roleAttachments.attachment.attachmentType',
            'applicationSelftape.applicationSelftapeAttachment.attachment',
            'applicationSelftape.ApplicationSelftapeAttachment.attachment.attachmentType',
            'audition.auditionMaterials.materialType',
            'role.project.genre',
            'role.project.user',
            'role.currency',
            'role.ethnicity',
            'role.personalSkills',
            'role.pickShootingDates',
            'role.roleAttachments.attachment',
            'role.roleAttachments.attachment.attachmentType',
        ]);

        if ($application->recommendRole?->relationLoaded('role')) {
            $application->recommendRole->role->loadExists([
                'actorBookmarks' => fn ($q) => $q->whereActorId(auth()->user()->getKey()),
            ]);
        }
    }
}
