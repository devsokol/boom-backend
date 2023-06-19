<?php

namespace Modules\MobileV1\Transformers;

use App\Models\Actor;
use App\Models\AttachmentType;
use App\Transformers\ActorAttachmentsHeadshotTransformer;
use App\Transformers\ActorAttachmentsSelftapeTransformer;
use App\Transformers\PersonalSkillTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ActorTransformer extends TransformerAbstract
{
    public function transform(Actor $actor): array
    {
        $data = [
            'id' => $actor->getKey(),
            'first_name' => $actor->first_name,
            'last_name' => $actor->last_name,
            'phone_number' => $actor->phone_number,
            'email' => $actor->email,
            'is_account_verified' => (bool) $actor->is_account_verified,
            'created_at' => $actor->created_at,
            'updated_at' => $actor->updated_at,
            'is_profile_created' => false,
        ];

        if ($actor->relationLoaded('actorInfo')) {
            $data['is_profile_created'] = (bool) $actor->actorInfo?->exists;

            $data['actorInfo'] = (object) fractal(
                $actor->actorInfo,
                new ActorInfoTransformer(),
                new ArraySerializer()
            )->toArray();
        }

        if ($actor->relationLoaded('actorSettings')) {
            $data['actorSettings'] = fractal(
                $actor->actorSettings,
                new ActorSettingTransformer(),
                new ArraySerializer()
            )->toArray();
        }

        if ($actor->relationLoaded('actorAttachments')) {
            if ($actor->actorAttachments->isNotEmpty()) {
                /**
                 * Headshot.
                 */
                $headshots = $actor->actorAttachments
                    ->filter(function ($item) {
                        return $item->attachment->attachment_type_id === AttachmentType::getType('headshot')->getKey();
                    });

                if ($headshots->isNotEmpty()) {
                    $data['headshots'] = fractal(
                        $headshots,
                        new ActorAttachmentsHeadshotTransformer(),
                        new ArraySerializer()
                    )->toArray();
                }

                /**
                 * Get video types.
                 * Selftape | Showreel | Presentation | Other.
                 */
                $videoAttachments = $actor->actorAttachments
                    ->filter(function ($item) {
                        return in_array($item->attachment->attachment_type_id, AttachmentType::getVideoTypesId());
                    });

                if ($videoAttachments->isNotEmpty()) {
                    $data['video'] = fractal(
                        $videoAttachments,
                        new ActorAttachmentsSelftapeTransformer(),
                        new ArraySerializer()
                    )->toArray();
                }
            }
        }

        if ($actor->relationLoaded('personalSkills')) {
            $data['personal_skills'] = fractal(
                $actor->personalSkills,
                new PersonalSkillTransformer(),
                new ArraySerializer()
            );
        }

        return $data;
    }
}
