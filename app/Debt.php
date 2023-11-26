<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $table = "debts";

    protected $fillable = [
        "customer_id", 
        "product_id", 
        "amount", 
        "due_date", 
        "amount_paid", 
        "debt_status_id", 
        "current_amount", 
        "product_price_change_date"
    ];
    
}
