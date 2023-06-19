<?php

namespace App\Models;

use App\Casts\ActingGender;
use App\Casts\ColorRange;
use App\Enums\PickShootingDateType;
use App\Enums\ProjectStatus;
use App\Enums\RoleStatus;
use App\Services\QueryCache\HasQueryCacheable;
use App\Traits\Model\HasProtectedRouteBinding;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Role extends BaseModel
{
    use HasFactory;
    use HasQueryCacheable;
    use Filterable;
    use HasProtectedRouteBinding;
    use HasEagerLimit;

    protected $fillable = [
        'name',
        'description',
        'dynamic_link',
        'rate',
        'label_range_color',
        'city',
        'address',
        'status',
        'acting_gender',
        'min_age',
        'max_age',
        'pick_shooting_date_type',
        'application_deadline',
        'country_id',
        'currency_id',
        'ethnicity_id',
        'payment_type_id',
        'project_id',
    ];

    protected $casts = [
        'rate' => 'integer',
        'label_range_color' => ColorRange::class,
        'status' => RoleStatus::class,
        'acting_gender' => ActingGender::class,
        'pick_shooting_date_type' => PickShootingDateType::class,
        'min_age' => 'integer',
        'max_age' => 'integer',
        'application_deadline' => 'datetime:Y-m-d',
    ];

    //region relation
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(Ethnicity::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function personalSkills(): BelongsToMany
    {
        return $this->belongsToMany(PersonalSkill::class, 'role_personal_skill');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function actorBookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class);
    }

    public function roleMaterials(): HasMany
    {
        return $this->hasMany(RoleMaterial::class);
    }

    public function roleAttachments(): HasMany
    {
        return $this->hasMany(RoleAttachment::class);
    }

    public function pickShootingDates(): HasMany
    {
        return $this->hasMany(RolePickShootingDate::class);
    }

    public function userViewedApplications(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_viewed_application')->withPivot('amount_viewed_applications');
    }

    public function recommendRoles(): HasMany
    {
        return $this->hasMany(RecommendRole::class);
    }
    //endregion relation

    //region scopes
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', RoleStatus::PUBLIC->value);
    }

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('status', RoleStatus::PRIVATE->value);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', RoleStatus::ARCHIVE->value);
    }

    public function scopeOnlyActiveProject(Builder $query): Builder
    {
        return $query->whereHas('project', function ($q) {
            return $q->where('status', ProjectStatus::ACTIVE->value);
        });
    }
    //endregion scopes

    public function isDeprecated(): bool
    {
        if (! $this->application_deadline) {
            return false;
        }

        return $this->application_deadline->hour(23)->minute(59)->second(59)->lessThan(now());
    }
}
