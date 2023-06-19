<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActorSetting extends BaseModel
{
    use HasFactory;

    protected $attributes = [
        'allow_app_notification' => true,
        'role_approve_notification' => true,
        'role_reject_notification' => true,
        'role_offer_notification' => true,
        'audition_notification' => true,
        'selftape_notification' => true,
    ];

    protected $fillable = [
        'allow_app_notification',
        'role_approve_notification',
        'role_reject_notification',
        'role_offer_notification',
        'audition_notification',
        'selftape_notification',
    ];

    protected $casts = [
        'allow_app_notification' => 'boolean',
        'role_approve_notification' => 'boolean',
        'role_reject_notification' => 'boolean',
        'role_offer_notification' => 'boolean',
        'audition_notification' => 'boolean',
        'selftape_notification' => 'boolean',
    ];

    //region relations
    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }
    //endregion relations
}
