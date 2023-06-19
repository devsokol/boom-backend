<?php

namespace App\ModelFilters;

use App\Enums\ProjectStatus;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class ProjectFilter extends ModelFilter
{
    public function filterByStatus(int $status): Builder|ProjectFilter
    {
        $cases = ProjectStatus::cases();

        $statues = array_column($cases, 'value');

        if (in_array($status, $statues)) {
            return $this->where('status', $status);
        }

        return $this;
    }
}
