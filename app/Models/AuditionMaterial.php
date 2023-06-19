<?php

namespace App\Models;

use App\Casts\Multimedia;
use App\Traits\Model\HasProtectedRouteBinding;
use App\Traits\Model\HasUploadMultimediaAdapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditionMaterial extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;
    use HasUploadMultimediaAdapter;

    protected $fillable = [
        'material_type_id',
        'attachment',
    ];

    public $timestamps = false;

    protected $casts = [
        'attachment' => Multimedia::class,
    ];

    //region relations
    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    public function audition(): BelongsTo
    {
        return $this->belongsTo(Audition::class);
    }
    //endregion relations
}
