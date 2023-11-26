<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    
    protected $fillable = ["user_id", "merchant_id", "customer_no", "name", "address", "contact_number", "email", "credit_limit"];

    public function debtStatus()
    {
        return $this->belongsTo('App\DebtStatus', 'debt_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
