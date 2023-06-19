<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $this->defineLocalization($request);

        app()->setLocale($locale);

        return $next($request);
    }

    private function defineLocalization(Request $request): string
    {
        if ($request->hasHeader('X-localization')) {
            return $request->header('X-localization');
        }

        return config('app.locale') ?? config('app.fallback_locale');
    }
}
