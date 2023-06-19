<?php

namespace App\Enums;

enum ProjectStatus: int
{
    case ACTIVE = 1;
    case ARCHIVED = 2;
    case DELETED = 3;

    public function status(): string
    {
        return match ($this) {
            self::ACTIVE => __('Active'),
            self::ARCHIVED => __('Archived'),
            self::DELETED => __('Deleted'),
        };
    }
}
