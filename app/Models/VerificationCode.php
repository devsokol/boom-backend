<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VerificationCode extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'code',
        'tag',
        'expires_at',
        'created_at',
    ];

    protected array $encryptable = [
        'code',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    //region relations
    public function codeable(): MorphTo
    {
        return $this->morphTo('codeable');
    }
    //endregion relations

    public static function isVerifyCodeValid(string|int $code, string $tag = 'verify'): bool
    {
        return static::query()
            ->toBase()
            ->select('code')
            ->where([['code', $code], ['tag', $tag], ['expires_at', '>', now()]])
            ->exists();
    }

    public static function getVerificationModelByCode(string|int $code): mixed
    {
        $res = self::where('code', $code)->first();

        if (! $res) {
            return false;
        }

        return $res->codeable()->first();
    }
}
