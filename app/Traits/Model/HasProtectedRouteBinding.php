<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Model;

trait HasProtectedRouteBinding
{
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return parent::resolveRouteBinding((int) $value, $field);
    }
}
