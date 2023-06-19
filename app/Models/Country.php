<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public $timestamps = false;

    //region relation
    public function country(): HasMany
    {
        return $this->hasMany(Role::class);
    }
    //endregion relation
}
