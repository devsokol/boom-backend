<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Services\QueryCache\HasQueryCacheable;
use App\Traits\Model\HasProtectedRouteBinding;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Application extends BaseModel
{
    use HasFactory;
    use Filterable;
    use HasQueryCacheable;
    use HasProtectedRouteBinding;
    use HasEagerLimit;

    protected $fillable = [
        'status',
        'actor_id',
        'role_id',
        'reject_reason',
    ];

    protected $casts = [
        'status' => ApplicationStatus::class,
        'actor_id' => 'integer',
        'role_id' => 'integer',
    ];

    //region roles
    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function audition(): HasOne
    {
        return $this->hasOne(Audition::class);
    }

    public function applicationSelftape()
    {
        return $this->hasOne(ApplicationSelftape::class);
    }

    public function actorNotifications(): HasMany
    {
        return $this->hasMany(ActorNotification::class);
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function recommendRole(): HasOne
    {
        return $this->hasOne(RecommendRole::class);
    }
    //endregion roles

    public function isActorDeleted(): bool
    {
        return is_null($this->actor_id);
    }
}
