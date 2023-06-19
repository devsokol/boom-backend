<?php

namespace App\Models;

use App\Casts\LocalVideo;
use App\Traits\Model\HasLocalVideo;
use App\Traits\Model\HasProtectedRouteBinding;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Selftape extends BaseModel
{
    use HasFactory;
    use HasLocalVideo;
    use HasProtectedRouteBinding;
    use HasEagerLimit;

    protected $fillable = [
        'video',
    ];

    protected $casts = [
        'video' => LocalVideo::class,
    ];

    //region relations
    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }
    //endregion relations

    public function filepathWithDate(): bool
    {
        return true;
    }
}
