<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "transactions";

    protected $fillable = [
        "customer_id",
        "transaction_id",
        "amount",
        "status",
        "payment_method",
        "transaction_date",
    ];
}
