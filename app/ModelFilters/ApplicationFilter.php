<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class ApplicationFilter extends ModelFilter
{
    public function filterByStatuses(array $ids): Builder|ApplicationFilter
    {
        return $this->whereIn('status', $ids);
    }
}
