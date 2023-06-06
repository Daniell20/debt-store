<?php

namespace App;

use App\Customer;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'email', 'password', 'is_admin', 'is_merchant', 'is_customer'
    ];

    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
}

