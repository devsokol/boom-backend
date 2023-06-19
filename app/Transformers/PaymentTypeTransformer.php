<?php

namespace App\Transformers;

use App\Models\PaymentType;
use League\Fractal\TransformerAbstract;

class PaymentTypeTransformer extends TransformerAbstract
{
    public function transform(PaymentType $paymentType): array
    {
        return [
            'id' => $paymentType->getKey(),
            'name' => $paymentType->name,
            'is_single' => $paymentType->is_single,
        ];
    }
}
