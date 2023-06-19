<?php

namespace App\Models;

use App\Casts\Base64Image;
use App\Enums\ProjectStatus;
use App\Services\QueryCache\HasQueryCacheable;
use App\Traits\Model\HasBase64ImageRequest;
use App\Traits\Model\HasProtectedRouteBinding;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends BaseModel
{
    use HasFactory;
    use HasBase64ImageRequest;
    use HasQueryCacheable;
    use Filterable;
    use HasProtectedRouteBinding;

    protected $fillable = [
        'placeholder',
        'name',
        'description',
        'start_date',
        'deadline',
        'status',
        'genre_id',
        'project_type_id',
        'user_id',
    ];

    protected $attributes = [
        'status' => 1, // project status by default
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d',
        'deadline' => 'datetime:Y-m-d',
        'status' => ProjectStatus::class,
        'placeholder' => Base64Image::class . ':jpg,75,1800,1800,placeholders',
    ];

    //region relation
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    //endregion relation

    //region scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProjectStatus::ACTIVE->value);
    }
    //endregion scopes

    public function filepathWithDate(): bool
    {
        return true;
    }

    public function getPlaceholder(): string
    {
        $placeholder = $this->placeholder;

        if (! $this->placeholder && $this->relationLoaded('genre')) {
            $placeholder = $this->genre->placeholder ?? '';
        }

        return $placeholder;
    }
}
