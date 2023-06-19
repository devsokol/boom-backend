<?php

namespace App\Models;

use App\Services\QueryCache\HasQueryCacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ProjectType extends BaseModel
{
    use HasFactory;
    use HasTranslations;
    use HasQueryCacheable;

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    protected array $translatable = ['name'];

    //region relation
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
    //endregion relation
}
