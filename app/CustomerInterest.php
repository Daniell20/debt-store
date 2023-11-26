<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerInterest extends Model
{
    protected $table = "customer_interests";

    protected $fillable = [
        "customer_id",
        "debt_id",
        "interest_rate",
        "calculation_date",
        "calculated_interest_amount",
        "debt_status_id",
    ];

    public function debtStatus()
    {
        return $this->belongsTo(DebtStatus::class, "debt_status_id", "id");
    }
}
