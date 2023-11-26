<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanSetting extends Model
{
    protected $table = "loan_settings";

    protected $fillable = [
        "merchant_id",
        "interest_rate",
        "months_to_pay"  
    ];
}
