<?php

namespace Modules\ApiV1\Helpers;

class Helper
{
    public static function frontendUrl(): string
    {
        return config('apiv1.frontend_url');
    }

    public static function fetchResetPasswordLink(string $token, string $email): string
    {
        return sprintf('%s/reset-password?token=%s&email=%s', self::frontendUrl(), $token, $email);
    }
}
