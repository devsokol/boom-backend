<?php

namespace App\Models;

use App\Casts\ActingGender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActorInfo extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'bio',
        'behance_link',
        'instagram_link',
        'youtube_link',
        'facebook_link',
        'actor_id',
        'ethnicity_id',
        'acting_gender',
        'min_age',
        'max_age',
        'pseudonym',
        'city',
    ];

    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
        'acting_gender' => ActingGender::class,
    ];

    //region relations
    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }

    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(Ethnicity::class);
    }
    //endregion relations
}
