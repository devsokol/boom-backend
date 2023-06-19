<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum Gender: int
{
    case MALE = 0;
    case FEMALE = 1;
    case OTHER = 2;

    public function gender(): string
    {
        return match ($this) {
            self::MALE => __('Male'),
            self::FEMALE => __('Female'),
            self::OTHER => __('Other'),
        };
    }

    public static function values(array $ids): Collection
    {
        return collect($ids)->map(fn ($item) => Gender::from($item)->gender());
    }
}
