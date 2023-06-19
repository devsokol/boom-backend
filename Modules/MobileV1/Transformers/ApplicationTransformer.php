<?php

namespace Modules\MobileV1\Transformers;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Services\Support\Fractal\Traits\HasRelations;
use App\Transformers\RecommendRoleTransformer;
use App\Transformers\RoleTransformer;
use League\Fractal\TransformerAbstract;
use Modules\ApiV1\Transformers\ApplicationSelftapeTransformer;
use Spatie\Fractalistic\ArraySerializer;

class ApplicationTransformer extends TransformerAbstract
{
    use HasRelations;

    public function __construct(private bool $expandedResponse = false)
    {
    }

    public function transform(Application $application): array
    {
        $data = [
            'id' => $application->getKey(),
            'status' => $application->status,
            'status_value' => ApplicationStatus::from($application->status->value)->status(),
        ];

        if ($this->expandedResponse) {
            $data['reject_reason'] = $application->reject_reason;
        }

        if ($application->relationLoaded('actor')) {
            $data['actor'] = fractal($application->actor, new ActorTransformer(), new ArraySerializer());
        }

        if ($application->relationLoaded('audition')) {
            $data['audition'] = $application->audition
                ? fractal($application->audition, new AuditionTransformer(), new ArraySerializer())
                : null;
        }

        if ($application->relationLoaded('applicationSelftape')) {
            $data['application_selftape'] = $application->applicationSelftape
                ? fractal(
                    $application->applicationSelftape,
                    new ApplicationSelftapeTransformer(),
                    new ArraySerializer()
                )
                : null;
        }

        if ($application->relationLoaded('role')) {
            $data['role'] = fractal($application->role, new RoleTransformer(true), new ArraySerializer());
        }

        if ($application->relationLoaded('recommendRole')) {
            $data['recommend_role'] = $application->recommendRole
                ? fractal($application->recommendRole, new RecommendRoleTransformer(), new ArraySerializer())
                : null;
        }

        return $data;
    }
}
