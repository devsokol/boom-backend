<?php

namespace Modules\ApiV1\Actions\Role;

use App\Dto\RoleData;
use App\Models\AttachmentType;
use App\Models\Project;
use App\Models\Role;
use App\Services\Common\Attachment\AttachmentService;
use Illuminate\Support\Facades\DB;

class RoleCreate
{
    /**
     * @var AttachmentService
     */
    public function __construct(protected AttachmentService $attachmentService)
    {
    }

    public function handle(RoleData $roleData, Project $project): Role
    {
        return DB::transaction(function () use ($roleData, $project) {
            $data = $roleData->except('personal_skills', 'materials')->toArray();

            $role = $project->roles()->create($data);

            (new GenerateDynamicLinkForRole())->handle($role, updateModel: true);

            $role->personalSkills()->sync($roleData->personal_skills);

            $pickShootingDates = $roleData->pick_shooting_dates ?? null;

            if ($pickShootingDates) {
                $role->pickShootingDates()->createMany($pickShootingDates);
            }

            $materials = $roleData->materials ?? null;

            if ($materials) {
                $attachments = [];

                foreach ($materials as $material) {
                    $attachment = $this->attachmentService->storeAttachment(
                        $material['attachment'],
                        null,
                        null,
                        AttachmentType::find($material['material_type_id'])
                    );
                    $this->attachmentService->reset();

                    $attachments[] = ['attachment_id' => $attachment->id];
                }

                $role->roleAttachments()->createMany($attachments);
            }

            return $role;
        });
    }
}
