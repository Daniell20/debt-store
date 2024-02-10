<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestrictedMerchant extends Model
{
    protected $table = "restricted_merchants";

    protected $fillable = [
        "merchant_id",
        "start_date",
        "end_date",
        "reason"
    ];
}
