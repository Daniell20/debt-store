<?php

namespace App\Http\Controllers\Auth;

use App\Merchants;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $check_customer = Merchants::where([
            ['name', '=', $data['name']],
        ])->count();

        if ($check_customer == 0) {
            $count_customer = Merchants::max('id');
            $user_credential = 'MERCH-' . str_pad('00000' + ($count_customer < 1 ? 1 : ++$count_customer), 5, '0', STR_PAD_LEFT);

            $create_customer = Customer::firstOrCreate([
                "name" => $data['name'],
                "merchant_no" => $user_credential
            ]);
            $create_customer -> debt_status_id = null;
            $create_customer -> save();
        } else {
            return redirect('/home')->with(['message' => 'Try again!']);
        }

        return User::create([
            'customer_id' => $create_customer->id,
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'is_admin' => 0
        ]);
    }
}
