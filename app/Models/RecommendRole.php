<?php

namespace App\Models;

use App\Enums\RecommendRoleStatus;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendRole extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    public $timestamps = false;

    protected $fillable = [
        'status',
        'role_id',
    ];

    protected $casts = [
        'status' => RecommendRoleStatus::class,
    ];

    //region roles
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    //endregion roles
}
