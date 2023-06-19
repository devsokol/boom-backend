<?php

namespace App\ModelFilters;

use App\Enums\RoleStatus;
use App\Services\Common\PaymentType\PaymentTypeService;
use Carbon\Carbon;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class RoleFilter extends ModelFilter
{
    /**
     * When a isSinglePaymentType property is set to TRUE,
     * then the fields: rate, currency are not included in the filtering.
     *
     * @var bool
     */
    private bool $isSinglePaymentType = false;

    /**
     * List of payment types IDs where the value is_single: true
     *
     * @var array
     */
    private array $singlePaymentTypeIds = [];

    public function __construct(mixed $query, array $input = [], $relationsEnabled = true)
    {
        parent::__construct($query, $input, $relationsEnabled);

        if (request()->has('filterByPaymentType')) {
            $paymentTypeId = request()->get('filterByPaymentType');

            $this->isSinglePaymentType = PaymentTypeService::getValueIsSingleByPaymentTypeId($paymentTypeId);
        }

        if (request()->has('disableSpecificPaymentTypes') || request()->has('filterByRate')) {
            $this->singlePaymentTypeIds = PaymentTypeService::getSpecificIds();
        }
    }

    public function filterByCompanies(array $ids): Builder|RoleFilter
    {
        return $this->where(function ($q) use ($ids) {
            return $q->whereHas('project.user', function ($q) use ($ids) {
                return $q->whereIn('id', $ids);
            });
        });
    }

    public function filterByGenres(array $genreIds): Builder|RoleFilter
    {
        return $this->where(function ($q) use ($genreIds) {
            return $q->whereHas('project', function ($q) use ($genreIds) {
                $q->whereIn('genre_id', $genreIds);
            });
        });
    }

    public function filterByStatus(int $status): Builder|RoleFilter
    {
        $cases = RoleStatus::cases();

        $statues = array_column($cases, 'value');

        if (in_array($status, $statues)) {
            return $this->where('status', $status);
        }

        return $this;
    }

    public function filterByRate(mixed $rate): Builder|RoleFilter
    {
        if (! $this->isSinglePaymentType && (isset($rate['min']) || isset($rate['max']))) {
            return $this->where(function ($q) use ($rate) {
                if (isset($rate['min']) && (int) $rate['min'] > 0) {
                    $q->whereNotIn('payment_type_id', $this->singlePaymentTypeIds);
                }

                if (isset($rate['min']) && isset($rate['max']) && $rate['max'] > $rate['min']) {
                    return $this->where([['rate', '>=', (int) $rate['min']], ['rate', '<=', (int) $rate['max']]]);
                } elseif (isset($rate['min']) && ! isset($rate['max'])) {
                    return $this->where('rate', '>=', (int) $rate['min']);
                } elseif (! isset($rate['min']) && isset($rate['max'])) {
                    return $this->where('rate', '<=', (int) $rate['max']);
                }
            });
        }

        return $this;
    }

    public function filterByCountry(mixed $countryId): Builder|RoleFilter
    {
        return $this->where('country_id', $countryId);
    }

    public function filterByLocations(mixed $cities): Builder|RoleFilter
    {
        return $this->whereIn('city', $cities);
    }

    public function filterByAddress(mixed $address): Builder|RoleFilter
    {
        return $this->where('address', $address);
    }

    public function filterByActingGender(mixed $actingGender): Builder|RoleFilter
    {
        return $this->where('acting_gender', $actingGender);
    }

    public function filterByCurrency(int $currencyId): Builder|RoleFilter
    {
        if (! $this->isSinglePaymentType) {
            return $this->where('currency_id', $currencyId);
        }

        return $this;
    }

    public function disableSpecificPaymentTypes(int|bool $isEnabled = false): Builder|RoleFilter
    {
        if ($isEnabled) {
            return $this->filterPaymentTypeExcludeIds($this->singlePaymentTypeIds);
        }

        return $this;
    }

    public function filterPaymentTypeExcludeIds(array $paymentTypeIds): Builder|RoleFilter
    {
        return $this->whereNotIn('payment_type_id', $paymentTypeIds);
    }

    public function filterByPaymentType(int $paymentTypeId): Builder|RoleFilter
    {
        return $this->where('payment_type_id', $paymentTypeId);
    }

    public function filterByPickShootingDates(array $range): Builder|RoleFilter
    {
        return $this->related('pickShootingDates', function ($query) use ($range) {
            return $this->filterByDate('date', $range, $query);
        });
    }

    public function filterByApplicationDeadline(array $range): Builder|RoleFilter
    {
        return $this->filterByDate('application_deadline', $range, $this);
    }

    private function filterByDate(
        string $filterableField,
        array $range,
        Builder|RoleFilter $builder
    ): Builder|RoleFilter
    {
        if (is_array($range)) {
            if (isset($range[0]) && isset($range[1])) {
                $startDate = $this->parseDate($range[0]);
                $endDate = $this->parseDate($range[1]);

                if ($endDate->greaterThan($startDate)) {
                    return $builder->whereBetween($filterableField, [$startDate, $endDate]);
                }
            } elseif (isset($range[0])) {
                return $builder->whereDate($filterableField, '>=', $this->parseDate($range[0]));
            } elseif (isset($range[1])) {
                return $builder->whereDate($filterableField, '<=', $this->parseDate($range[1]));
            }
        }

        return $this;
    }

    private function parseDate(string $date): Carbon
    {
        return Carbon::parse($date);
    }
}
