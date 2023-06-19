<?php

namespace App\Models;

use App\Services\QueryCache\HasQueryCacheable;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class MaterialType extends BaseModel
{
    use HasFactory;
    use HasTranslations;
    use HasQueryCacheable;
    use HasProtectedRouteBinding;

    protected array $translatable = ['name'];

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public static array $allowedExtensions = [
        'pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'ods', 'csv', 'xls', 'xlsx', // documents
        'png', 'jpg', 'jpeg', 'bmp', 'gif', 'heic', 'webp', // images types
        'mov', 'mp4', '3gp', // video types
    ];

    //region relations
    public function roleMaterials(): HasMany
    {
        return $this->hasMany(RoleMaterial::class);
    }
    //endregion relations
}
