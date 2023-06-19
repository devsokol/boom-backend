<?php

namespace App\Models;

use App\Services\QueryCache\HasQueryCacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Ethnicity extends BaseModel
{
    use HasFactory;
    use HasTranslations;
    use HasQueryCacheable;

    protected array $translatable = ['name'];

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    //region relations
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }
    //endregion relations
}
