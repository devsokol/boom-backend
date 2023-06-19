<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Project;
use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Collection;
use App\Models\AttachmentType;

class RoleList
{
    public function handle(Project $project, array $filter = []): Collection
    {
        $userId = auth()->user()->getKey();

        return $project
                ->roles()
                ->with([
                    'ethnicity',
                    'userViewedApplications' => function ($q) use ($userId) {
                        $q->select('id')->whereUserId($userId);
                    },
                    'applications' => function ($query) {
                        $query->whereNotNull('actor_id');
                        $query->with('actor', function ($query) {
                            $query->with('actorAttachments', function ($query) {
                                // TODO | need add limit
                                $query
                                    ->with('attachment')
                                    ->whereHas('attachment', function ($query) {
                                        $query->where('attachment_type_id', AttachmentType::getHeadshot()->getKey());
                                    });
                            });
                        })
                        ->latest()
                        ->limit(2);
                    },
                ])
                ->withCount(['applications' => function ($q) {
                    $q->whereNotNull('actor_id');
                }])
                ->withCount(['applications as deleted_applications_count' => function ($q) {
                    $q->whereNull('actor_id');
                }])
                ->withCount(['applications as applications_approved_count' => function ($q) {
                    $q->where('status', ApplicationStatus::APPROVED->value);
                }])
                ->withCount(['applications as applications_approval_count' => function ($q) {
                    $q->where('status', ApplicationStatus::APPROVAL->value);
                }])
                ->withCount(['applications as applications_audition_count' => function ($q) {
                    $q->where('status', ApplicationStatus::AUDITION->value);
                }])
                ->filter($filter)
                ->oldest()
                ->get();
    }
}
