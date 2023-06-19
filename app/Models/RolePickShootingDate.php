<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePickShootingDate extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'date',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    //region relations
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    //endregion relations
}
