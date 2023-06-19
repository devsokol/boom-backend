<?php

namespace App\Enums;

enum ApplicationSelftapeStatus: int
{
    case IN_REVIEW = 0;
    case SENDED = 1;
    case REJECTED = 2;

    public function status(): string
    {
        return match ($this) {
            self::IN_REVIEW => __('In review'),
            self::SENDED => __('Sended'),
            self::REJECTED => __('Rejected'),
        };
    }
}
