<?php

namespace App\Models;

use App\Services\QueryCache\HasQueryCacheable;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class PersonalSkill extends BaseModel
{
    use HasFactory;
    use HasTranslations;
    use HasQueryCacheable;
    use HasProtectedRouteBinding;

    protected array $translatable = ['name'];

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    //region relations
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_personal_skill');
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'actor_personal_skill');
    }
    //endregion relations
}
