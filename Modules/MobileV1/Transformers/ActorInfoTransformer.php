<?php

namespace Modules\MobileV1\Transformers;

use App\Enums\Gender;
use App\Models\ActorInfo;
use League\Fractal\TransformerAbstract;
use App\Transformers\EthnicityTransformer;
use App\Services\Support\Fractal\Traits\HasRelations;

class ActorInfoTransformer extends TransformerAbstract
{
    use HasRelations;

    protected array $defaultIncludes = [
        'ethnicity',
    ];

    public function transform(ActorInfo $actorInfo): array
    {
        return [
            'id' => $actorInfo->getKey(),
            'bio' => $actorInfo->bio,
            'behance_link' => $actorInfo->behance_link,
            'instagram_link' => $actorInfo->instagram_link,
            'youtube_link' => $actorInfo->youtube_link,
            'facebook_link' => $actorInfo->facebook_link,
            'acting_gender' => $actorInfo->acting_gender,
            'acting_gender_values' => Gender::values((array) $actorInfo->acting_gender),
            'min_age' => $actorInfo->min_age,
            'max_age' => $actorInfo->max_age,
            'pseudonym' => $actorInfo->pseudonym,
            'city' => $actorInfo->city,

            'created_at' => $actorInfo->created_at,
            'updated_at' => $actorInfo->updated_at,
        ];
    }

    public function includeEthnicity(ActorInfo $actorInfo): mixed
    {
        return $this->itemHasRelation($actorInfo, 'ethnicity', new EthnicityTransformer());
    }
}
