<?php

namespace App\Transformers;

use App\Models\PersonalSkill;
use League\Fractal\TransformerAbstract;

class PersonalSkillTransformer extends TransformerAbstract
{
    public function transform(PersonalSkill $personalSkill): array
    {
        return [
            'id' => $personalSkill->getKey(),
            'name' => $personalSkill->name,
        ];
    }
}
