<?php

namespace Modules\ApiV1\Transformers;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Services\Common\Role\RoleMatchService;
use App\Services\Support\Fractal\Traits\HasRelations;
use App\Transformers\RoleTransformer;
use League\Fractal\TransformerAbstract;
use Modules\MobileV1\Transformers\ActorTransformer;
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
            $data['actor'] = $application->actor
                ? fractal($application->actor, new ActorTransformer(), new ArraySerializer())
                : null;
        }

        if ($application->relationLoaded('role')) {
            $data['role'] = fractal($application->role, new RoleTransformer(), new ArraySerializer());
        }

        if ($this->expandedResponse && $application->actor) {
            $data['matches_found'] = (new RoleMatchService($application->role, $application->actor))->matches();
        }

        return $data;
    }
}
