<?php

namespace App\Models;

use App\Exceptions\PropertyNotFoundException;
use App\Traits\Model\HasAuthSanctum;
use App\Traits\Model\HasProtectedRouteBinding;
use App\Traits\Model\HasVerificationCode;
use DateTimeInterface;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\QueryException;
use Illuminate\Notifications\Notifiable;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Actor extends BaseModel implements CanResetPasswordContract
{
    use CanResetPassword;
    use HasFactory;
    use HasAuthSanctum{
        createToken as parentCreateToken;
    }
    use HasVerificationCode;
    use HasProtectedRouteBinding;
    use HasEagerLimit;
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'is_account_verified',
        'email',
        'password',
        'fcm_token',
        'is_deleted',
    ];

    protected $casts = [
        'is_account_verified' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    //region relations
    public function actorNotifications(): HasMany
    {
        return $this->hasMany(ActorNotification::class);
    }

    public function actorSettings(): HasOne
    {
        return $this->hasOne(ActorSetting::class)->withDefault();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function actorInfo(): HasOne
    {
        return $this->hasOne(ActorInfo::class);
    }

    // TODO
    public function headshots(): HasMany
    {
        return $this->hasMany(Headshot::class);
    }

    // TODO
    public function selftapes(): HasMany
    {
        return $this->hasMany(Selftape::class);
    }

    public function roleBookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function personalSkills(): BelongsToMany
    {
        return $this->belongsToMany(PersonalSkill::class, 'actor_personal_skill');
    }

    public function actorAttachments()
    {
        return $this->hasMany(ActorAttachments::class);
    }
    //endregion relations

    /**
     * Overriding the method prevent duplicate key value unique error.
     *
     * @param  string  $name
     * @param  array  $abilities
     * @param  DateTimeInterface|null  $expiresAt
     * @return mixed
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

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }

    /**
     * Specifies the user's FCM tokens.
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    public function isNotificationEnabled(string $property): bool
    {
        if (! isset($this->actorSettings->{$property})) {
            throw new PropertyNotFoundException(sprintf('Property: [%s] not found in actor settings', $property));
        }

        return isset($this->actorSettings->{$property}) ? $this->actorSettings->{$property} : false;
    }

    public function isMarkAsDeleted(): bool
    {
        return (bool) $this->is_deleted;
    }

    public function markAsDeleted(): void
    {
        $this->is_deleted = true;
        $this->save();
    }
}
