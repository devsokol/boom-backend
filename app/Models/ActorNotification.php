<?php

namespace App\Models;

use App\Enums\ActorNotificationStatus;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActorNotification extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    protected $fillable = [
        'title',
        'body',
        'status',
        'is_read',
        'actor_id',
        'application_id',
    ];

    protected $casts = [
        'status' => ActorNotificationStatus::class,
        'is_read' => 'boolean',
    ];

    //region relations
    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    //endregion relations
}
