<?php

namespace App\Services\Common\PaymentType;

use App\Models\PaymentType;

class PaymentTypeService
{
    public static function getValueIsSingleByPaymentTypeId(int $id): bool
    {
        $paymentType = PaymentType::toBase()->select('is_single')->whereId($id)->first();

        return (bool) optional($paymentType)->is_single;
    }

    public static function getSpecificIds(): array
    {
        return PaymentType::toBase()->select('id')->whereIsSingle(true)->pluck('id')->toArray();
    }
}
