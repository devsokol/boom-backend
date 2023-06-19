<?php

namespace App\Enums;

enum UserNotificationStatus: int
{
    case AUDITION_ACCEPTED = 0;
    case AUDITION_DECLINED = 1;
    case ROLE_ACCEPTED = 10;
    case ROLE_DECLINED = 11;
    case OFFER_ROLE_ACCEPTED = 20;
    case OFFER_ROLE_DECLINED = 21;

    public function status(): string
    {
        return match ($this) {
            self::AUDITION_ACCEPTED => __('Audition accepted'),
            self::AUDITION_DECLINED => __('Audition declined'),
            self::ROLE_ACCEPTED => __('Role accepted'),
            self::ROLE_DECLINED => __('Role declined'),
            self::OFFER_ROLE_ACCEPTED => __('New role accepted'),
            self::OFFER_ROLE_DECLINED => __('New role declined'),
        };
    }
}
