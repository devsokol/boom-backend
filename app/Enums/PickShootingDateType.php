<?php

namespace App\Enums;

enum PickShootingDateType: int
{
    case SINGLE = 0;
    case MULTI = 1;
    case RANGE = 2;

    public function types(): string
    {
        return match ($this) {
            self::SINGLE => __('Single'),
            self::MULTI => __('Multi-days'),
            self::RANGE => __('Date\'s range'),
        };
    }
}
