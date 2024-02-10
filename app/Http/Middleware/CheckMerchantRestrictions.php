<?php

namespace App\Http\Middleware;

use App\Merchant;
use App\RestrictedMerchant;
use App\User;
use Carbon\Carbon;
use Closure;

class CheckMerchantRestrictions
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
        $this->checkMerchantRestrictions();

        return $next($request);
    }

    private function checkMerchantRestrictions()
    {
        $restrictedMerchants = RestrictedMerchant::where('end_date', Carbon::now()->toDateString())->get();
        if ($restrictedMerchants) {
            foreach ($restrictedMerchants as $merchant) {
                $user_merchant = Merchant::find($merchant->merchant_id);
                $user = User::find($user_merchant->user_id);
                $user->is_restricted = 0;
                $user->update();
            }
        }
    }
}
