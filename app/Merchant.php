<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $fillable = ['name', 'merchants_no', 'user_id'];

    protected $table = 'merchants';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
