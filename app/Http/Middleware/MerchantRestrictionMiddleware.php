<?php

namespace App\Http\Middleware;

use Closure;

class MerchantRestrictionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->is_merchant && auth()->user()->is_restricted) {
            return response()->json(['error' => 'Merchant is restricted.'], 403);
        }

        return $next($request);
    }
}
