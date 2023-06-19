<?php

namespace App\Services\Common\Role;

use App\Models\Currency;
use App\Models\Genre;
use App\Models\PaymentType;
use App\Models\Role;
use App\Models\User;
use App\Transformers\CurrencyTransformer;
use App\Transformers\GenreTransformer;
use App\Transformers\PaymentTypeTransformer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Fractalistic\ArraySerializer;

class FilterRoleService
{
    public function tools(?int $currencyId = null, ?int $paymentTypeId = null): array
    {
        $data = [];

        $data['min_max_rate'] = $this->minMaxRate($currencyId, $paymentTypeId);

        $data['genres'] = Genre::makeCacheByUniqueRequest(function () {
            return fractal(Genre::all(), new GenreTransformer())->serializeWith(new ArraySerializer());
        });

        $data['cities'] = Role::makeCacheByUniqueRequest(function () {
            return $this->getCitiesList();
        });

        $data['companies'] = User::makeCacheByUniqueRequest(function () {
            return User::toBase()->select('id', 'company_name')->get();
        });

        $data['payment_types'] = PaymentType::makeCacheByUniqueRequest(function () {
            return fractal(PaymentType::all(), new PaymentTypeTransformer())->serializeWith(new ArraySerializer());
        });

        $data['currencies'] = Currency::makeCacheByUniqueRequest(function () {
            return fractal(Currency::all(), new CurrencyTransformer())->serializeWith(new ArraySerializer());
        });

        return $data;
    }

    public function getCitiesList(): ?Collection
    {
        return Role::toBase()->select('city')->whereNotNull('city')->groupBy('city')->orderBy('city')->get();
    }

    public function minMaxRate(?int $currencyId = null, ?int $paymentTypeId = null): Collection
    {
        return Role::query()
            ->toBase()
            ->select(DB::raw('coalesce(MAX(rate), 0) AS max_rate'), DB::raw('coalesce(MIN(rate), 0) AS min_rate'))
            ->where(function ($q) use ($currencyId, $paymentTypeId) {
                if ($currencyId) {
                    $q->where('currency_id', $currencyId);
                }

                if ($paymentTypeId) {
                    $q->where('payment_type_id', $paymentTypeId);
                }
            })
            ->get();
    }
}
