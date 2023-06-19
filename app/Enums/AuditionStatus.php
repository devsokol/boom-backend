<?php

namespace App\Enums;

enum AuditionStatus: int
{
    case IN_REVIEW = 0;
    case ACCEPTED = 1;
    case REJECTED = 2;

    public function status(): string
    {
        return match ($this) {
            self::IN_REVIEW => __('In review'),
            self::ACCEPTED => __('Accepted'),
            self::REJECTED => __('Rejected'),
        };
    }
}
