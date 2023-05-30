<?php

use Illuminate\Database\Seeder;
use App\DebtStatus;

class DebtStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $debt_statuses = [
            [
                'name' => 'Paid'
            ],
            [
                'name' => 'Unpaid'
            ],
            [
                'name' => 'Initial Deposit'
            ],
            [
                'name' => 'Gekalimtan'
            ],
        ];
        
        foreach($debt_statuses as $debt) {
            DebtStatus::create($debt);
        }
    }
}
