<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $user = auth()->user();

        if ($user) {
            $roleMapping = [
                'admin' => 'is_admin',
                'merchant' => 'is_merchant',
                'customer' => 'is_customer',
            ];
            
            $userRole = isset($roleMapping[$role]) ? $roleMapping[$role] : null;
            
            if ($userRole && $user->$userRole && $user->is_active) {
                return $next($request);
            }
        }

        return redirect('login')->with('error', 'You do not have access to this page or contact the administrator to activate your account!');
    }
}
