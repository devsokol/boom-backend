<?php

namespace App\Models;

use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationSelftapeAttachment extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;

    protected $table = 'application_selftape_attachment';

    protected $fillable = [
        'attachment_id',
        'application_selftape_id',
        'is_actor',
    ];

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
    // protected $fillable = [];
    // protected $hidden = ['password', 'remember_token'];
    // protected $dates = [];
    // protected $perPage = 25;

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Define the inverse one-to-one or one-to-many relationship with the Attachment model.
     *
     * @return BelongsTo The relationship instance.
     */
    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'attachment_id', 'id');
    }

    /**
     * Define the inverse one-to-one or one-to-many relationship with the ApplicationSelftape model.
     *
     * @return BelongsTo The relationship instance.
     */
    public function applicationSelftape(): BelongsTo
    {
        return $this->belongsTo(ApplicationSelftape::class, 'id', 'application_selftape_id');
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
