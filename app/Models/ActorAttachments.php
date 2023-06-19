<?php

namespace App\Models;

use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActorAttachments extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return hasOne
     */
    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'id', 'attachment_id');
    }

    /**
     * @return hasOne
     */
    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }
}
