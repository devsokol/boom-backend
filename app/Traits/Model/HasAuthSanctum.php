<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;

trait HasAuthSanctum
{
    use HasApiTokens;

    //region mutators
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }
    //endregion mutators
}
