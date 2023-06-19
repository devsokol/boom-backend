<?php

namespace App\Models;

use App\Enums\ApplicationSelftapeStatus;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationSelftape extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    public $timestamps = false;

    protected $fillable = [
        'description',
        'deadline_datetime',
        'status',
    ];

    protected $casts = [
        'status' => ApplicationSelftapeStatus::class,
        'deadline_datetime' => 'datetime',
    ];

    //region relations
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * @return hasMany
     */
    public function applicationSelftapeAttachment()
    {
        return $this->hasMany(ApplicationSelftapeAttachment::class, 'application_selftape_id', 'id');
    }

    // public function applicationSelftapeMaterials(): HasMany
    // {
    //     return $this->hasMany(ApplicationSelftapeMaterial::class);
    // }
    //endregion relations
}
