<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        if (auth()->user()->is_admin == 1) {
            return '/admin/dashboard';
        } else if (auth()->user()->is_merchant == 1) {
            if (auth()->user()->is_password_change == 0) {
                return "merchant/change_password";
            } else {
                return '/merchant/dashboard';
            }
        } else if (auth()->user()->is_customer == 1) {
            if (auth()->user()->is_password_change == 0) {
                return "customer/change_password";
            } else {
                return 'customer/home';
            }
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
