<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebtInterest extends Model
{
    protected $table = "debt_interests";

    protected $fillable = [
        "debt_id",
        "loan_setting_id",
        "start_date",
        "end_date"
    ];
}
