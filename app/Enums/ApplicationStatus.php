<?php

namespace App\Enums;

enum ApplicationStatus: int
{
    case IN_REVIEW = 0;
    case APPROVED = 10;
    case APPROVAL = 11;
    case UNDERSTUDY = 20;
    case REJECTED_BY_OWNER = 30;
    case REJECTED_BY_ACTOR = 31;
    case AUDITION = 40;
    case SELFTAPE_REQUEST = 50;
    case SELFTAPE_PROVIDED = 51;
    case ROLE_OFFER = 60;

    public function status(): string
    {
        return match ($this) {
            self::IN_REVIEW => __('In review'),
            self::APPROVED => __('Approved'),
            self::APPROVAL => __('Approval request'),
            self::UNDERSTUDY => __('Understudy'),
            self::REJECTED_BY_OWNER => __('Rejected by owner'),
            self::REJECTED_BY_ACTOR => __('Rejected by actor'),
            self::AUDITION => __('Audition'),
            self::SELFTAPE_REQUEST => __('Selftape request'),
            self::SELFTAPE_PROVIDED => __('Selftape provided'),
            self::ROLE_OFFER => __('Offer another role'),
        };
    }
}
