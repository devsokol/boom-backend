<?php

namespace App\Transformers;

use App\Models\Ethnicity;
use League\Fractal\TransformerAbstract;

class EthnicityTransformer extends TransformerAbstract
{
    public function transform(Ethnicity $ethnicity): array
    {
        return [
            'id' => $ethnicity->getKey(),
            'name' => $ethnicity->name,
        ];
    }
}
