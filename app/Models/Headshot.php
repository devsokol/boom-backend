<?php

namespace App\Models;

use App\Casts\Base64Image;
use App\Traits\Model\HasBase64ImageRequest;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Headshot extends BaseModel
{
    use HasFactory;
    use HasBase64ImageRequest;
    use HasProtectedRouteBinding;

    protected $fillable = [
        'headshot',
    ];

    protected $casts = [
        'headshot' => Base64Image::class . ':jpg,75,1800,1800',
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
