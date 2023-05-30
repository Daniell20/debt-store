<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    
    protected $fillable = ['name', 'customer_id'];

    public function debtStatus()
    {
        return $this->belongsTo('App\DebtStatus', 'debt_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
