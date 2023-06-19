<?php

namespace App\Models;

use App\Services\QueryCache\HasQueryCacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PaymentType extends BaseModel
{
    use HasFactory;
    use HasTranslations;
    use HasQueryCacheable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'is_single',
    ];

    protected array $translatable = ['name'];

    protected $casts = [
        'is_single' => 'boolean',
    ];

    //region relation
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }
    //endregion relation
}
