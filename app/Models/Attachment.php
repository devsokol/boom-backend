<?php

namespace App\Models;

use App\Casts\LocalVideo;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'attachment_repository' => LocalVideo::class,
    ];

    /**
     * Define a many-to-one relationship with the AttachmentType model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attachmentType()
    {
        return $this->belongsTo(AttachmentType::class);
    }

    public function applicationSelftapeAttachment()
    {
        return $this->hasOne(ApplicationSelftapeAttachment::class);
    }
}
