<?php

namespace App\Models;

use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleAttachment extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    // const CREATED_AT = 'created_date';
    // const UPDATED_AT = 'last_update';

    // protected $table = 'users';
    // protected $primaryKey = 'your_key_name'; // or null
    // public $incrementing = false;
    public $timestamps = false;
    // protected $touches = ['post'];

    // protected $appends = [];
    // protected $connection = 'conn-name';
    // protected $casts = ['is_admin' => 'boolean'];
    // protected $guarded = ['id_test'];
    // protected $dateFormat = 'd-m-Y';
    protected $fillable = [
        'attachment_id',
        'role_id',
    ];
    // protected $hidden = ['password', 'remember_token'];
    // protected $dates = [];
    // protected $perPage = 25;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
}
