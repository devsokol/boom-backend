<?php

namespace App\Models;

use App\Casts\Multimedia;
use App\Services\QueryCache\HasQueryCacheable;
use App\Traits\Model\HasUploadMultimediaAdapter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Genre extends BaseModel
{
    use HasFactory;
    use HasTranslations;
    use HasQueryCacheable;
    use HasUploadMultimediaAdapter;

    protected $fillable = [
        'name',
        'icon',
        'placeholder',
    ];

    public $timestamps = false;

    protected array $translatable = ['name'];

    protected $casts = [
        'icon' => Multimedia::class . ':false,null,true',
        'placeholder' => Multimedia::class . ':false,null,true',
    ];

    //region relation
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
    //endregion relation

    public function anonymousFilePath(): bool
    {
        return true;
    }
}
