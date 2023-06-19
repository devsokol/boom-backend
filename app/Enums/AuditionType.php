<?php

namespace App\Enums;

enum AuditionType: int
{
    case OFFLINE = 0;
    case ONLINE = 1;

    public function types(): string
    {
        return match ($this) {
            self::OFFLINE => __('Offline'),
            self::ONLINE => __('Online'),
        };
    }
}
