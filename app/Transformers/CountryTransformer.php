<?php

namespace App\Transformers;

use App\Models\Country;
use League\Fractal\TransformerAbstract;

class CountryTransformer extends TransformerAbstract
{
    public function transform(Country $country): array
    {
        return [
            'id' => $country->getKey(),
            'name' => $country->name,
            'code' => $country->code,
        ];
    }
}
