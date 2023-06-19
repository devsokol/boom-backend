<?php

namespace App\Enums;

enum RoleStatus: int
{
    case PUBLIC = 0;
    case PRIVATE = 1;
    case ARCHIVE = 2;

    public function status(): string
    {
        return match ($this) {
            self::PUBLIC => __('Public'),
            self::PRIVATE => __('Private'),
            self::ARCHIVE => __('Archive'),
        };
    }
}
