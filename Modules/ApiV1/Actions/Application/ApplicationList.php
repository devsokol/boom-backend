<?php

namespace Modules\ApiV1\Actions\Application;

use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\AttachmentType;

class ApplicationList
{
    public function handle(Role $role, array $filter): LengthAwarePaginator
    {
        return $role
            ->applications()
            ->filter($filter)
            ->with([
                'actor.actorInfo.ethnicity',
                'actor.personalSkills',
                'actor.actorAttachments' => function ($query) {
                    $query
                        ->with('attachment')
                        ->whereHas('attachment', function ($query) {
                            $query->where('attachment_type_id', AttachmentType::getHeadshot()->getKey());
                        });
                },
            ])
            ->latest()
            ->jsonPaginate();
    }
}
