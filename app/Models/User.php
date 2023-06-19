<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\Base64Image;
use App\Services\QueryCache\HasQueryCacheable;
use App\Traits\Model\HasAuthSanctum;
use App\Traits\Model\HasBase64ImageRequest;
use App\Traits\Model\HasProtectedRouteBinding;
use App\Traits\Model\HasVerificationCode;
use DateTimeInterface;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Notifications\Notifiable;

class User extends BaseModel implements CanResetPasswordContract
{
    use CanResetPassword;
    use HasFactory;
    use HasAuthSanctum{
        createToken as parentCreateToken;
    }
    use HasBase64ImageRequest;
    use Notifiable;
    use HasVerificationCode;
    use HasQueryCacheable;
    use HasProtectedRouteBinding;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'avatar',
        'company_name',
        'phone_number',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'avatar' => Base64Image::class . ':jpg,75,512,512,avatar',
    ];

    //region relation
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function roleViewedApplications(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_viewed_application')->withPivot('amount_viewed_applications');
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }
    //endregion relation

    //region mutators
    protected function phoneNumber(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => str_replace(['(', ')', ' '], '', $value),
        );
    }
    //endregion mutators

    public function filepathWithDate(): bool
    {
        return true;
    }

    /**
     * Overriding the method prevent duplicate key value unique error.
     *
     * @param  string  $name
     * @param  array  $abilities
     * @param  DateTimeInterface|null  $expiresAt
     * @return void
     */
    public function createToken(string $name, array $abilities = ['*'], DateTimeInterface $expiresAt = null)
    {
        try {
            return $this->parentCreateToken($name, $abilities, $expiresAt);
        } catch (QueryException $e) {
            $code = intval($e->getCode());

            if ($code === PGSQL_DUPLICATE_KEY_ERROR_CODE || $code === MYSQL_DUPLICATE_KEY_ERROR_CODE) {
                return $this->createToken($name, $abilities, $expiresAt);
            }
        }
    }
}
