<?php

namespace App\Models;

use App\Enums\AuditionStatus;
use App\Enums\AuditionType;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audition extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    public $timestamps = false;

    protected $attributes = [
        'status' => AuditionStatus::IN_REVIEW,
    ];

    protected $fillable = [
        'type',
        'address',
        'audition_datetime',
        'status',
        'reject_reason',
    ];

    protected $casts = [
        'type' => AuditionType::class,
        'status' => AuditionStatus::class,
        'audition_datetime' => 'datetime',
    ];

    //region relations
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function auditionMaterials(): HasMany
    {
        return $this->hasMany(AuditionMaterial::class);
    }
    //endregion relations
}
