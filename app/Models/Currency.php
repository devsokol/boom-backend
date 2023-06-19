<?php

namespace App\Models;

use App\Services\QueryCache\HasQueryCacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends BaseModel
{
    use HasFactory;
    use HasQueryCacheable;

    protected $fillable = [
        'name',
        'code',
        'symbol',
    ];

    public $timestamps = false;

    //region relations
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }
    //endregion relations
}
