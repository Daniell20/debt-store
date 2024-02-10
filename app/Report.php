<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = "reports";
    protected $fillable = [
        "customer_id",
        "merchant_id",
        "description"
    ];
}
