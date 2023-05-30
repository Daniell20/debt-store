<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebtStatus extends Model
{
    protected $table = 'debt_statuses';

    public function customers()
    {
        return $this->hasMany('App\Customer', 'debt_status_id');
    }
}
