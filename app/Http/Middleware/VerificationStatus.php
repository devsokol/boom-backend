<?php

namespace App\Http\Middleware;

use App\Traits\Model\HasVerificationCode;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerificationStatus
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->user() ||
            (isExistsTraitInClass(HasVerificationCode::class, $request->user()) &&
            ! $request->user()->hasVerifiedStatus())) {
            abort(Response::HTTP_FORBIDDEN, __('Your account has not been verified.'));
        }

        return $next($request);
    }
}
