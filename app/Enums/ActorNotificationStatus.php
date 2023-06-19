<?php

namespace App\Enums;

enum ActorNotificationStatus: int
{
    case ROLE_OFFERED = 0;
    case REJECTED = 10;
    case ROLE_OFFER = 20;
    case AUDITION = 30;
    case SELFTAPE_REQUEST = 40;

    public function status(): string
    {
        return match ($this) {
            self::ROLE_OFFERED => __('Role offered'),
            self::REJECTED => __('Rejected'),
            self::AUDITION => __('Audition'),
            self::SELFTAPE_REQUEST => __('Selftape request'),
            self::ROLE_OFFER => __('Offer another role'),
        };
    }
}
