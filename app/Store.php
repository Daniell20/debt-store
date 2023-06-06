<?php

namespace App;
//store product

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'email', 'user_id', 'merchant_id'
    ];
}
