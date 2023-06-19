<?php

namespace App\Models;

use App\Enums\UserNotificationStatus;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    protected $fillable = [
        'title',
        'body',
        'status',
        'is_read',
        'user_id',
        'application_id',
    ];

    protected $casts = [
        'status' => UserNotificationStatus::class,
        'is_read' => 'boolean',
    ];

    //region relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    //endregion relations
}
