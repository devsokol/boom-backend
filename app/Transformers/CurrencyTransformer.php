<?php

namespace App\Transformers;

use App\Models\Currency;
use League\Fractal\TransformerAbstract;

class CurrencyTransformer extends TransformerAbstract
{
    public function transform(Currency $currency): array
    {
        return [
            'id' => $currency->getKey(),
            'name' => $currency->name,
            'code' => $currency->code,
            'symbol' => $currency->symbol,
        ];
    }
}
